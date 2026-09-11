<?php
// admin_portal/student_profile.php
require_once 'auth.php';
require_once 'db_config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) die("Invalid student ID.");

$user_id = get_current_user_id();
$is_super_admin = is_super_admin();

$stmt = $pdo->prepare("SELECT c.*, u.username as assigned_to_name FROM consultations c LEFT JOIN users u ON c.assigned_to = u.id WHERE c.id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) die("Student not found.");
if (!$is_super_admin && $student['assigned_to'] !== null && $student['assigned_to'] != $user_id) {
    die("Unauthorized access to this student profile.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['processing_status'];
    $updateStmt = $pdo->prepare("UPDATE consultations SET processing_status = ? WHERE id = ?");
    $updateStmt->execute([$new_status, $id]);
    $student['processing_status'] = $new_status; 
    log_activity($pdo, get_current_user_id(), "Updated processing status to '$new_status' for lead ID: $id");
}

$timeline_stages = [
    'Pending', 'Applied to University', 'Offer letter issued', 
    'EMGS Paid', 'EMGS Online', 'Visa Approved', 'Visa Rejected', 'EMGS Rejected'
];

$current_stage_index = array_search($student['processing_status'], $timeline_stages);
if ($current_stage_index === false) $current_stage_index = 0;

$progress_percent = 0;
if ($current_stage_index > 0) {
    $max_positive_step = 5; 
    $effective_index = min($current_stage_index, $max_positive_step);
    $progress_percent = ($effective_index / $max_positive_step) * 100;
}

$stmtNotes = $pdo->prepare("SELECT n.*, u.username FROM notes n JOIN users u ON n.user_id = u.id WHERE n.consultation_id = ? ORDER BY n.created_at DESC");
$stmtNotes->execute([$id]);
$notes = $stmtNotes->fetchAll();

$stmtDocs = $pdo->prepare("SELECT * FROM documents WHERE consultation_id = ? ORDER BY uploaded_at DESC");
$stmtDocs->execute([$id]);
$documents = $stmtDocs->fetchAll();

$phone = preg_replace('/[^0-9]/', '', $student['phone_number']);
if (strpos($phone, '880') === 0) { $wa_num = $phone; }
elseif (strpos($phone, '0') === 0) { $wa_num = '88' . $phone; }
else { $wa_num = '880' . $phone; }
$wa_link = "https://wa.me/" . $wa_num;

include 'layout_top.php';
?>
<input type="hidden" id="customPageTitle" value="Student Profile: <?php echo htmlspecialchars($student['full_name']); ?>">

<style>
    .profile-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
    
    .card-title { font-size: 16px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
    .card-title i { color: var(--primary); }
    
    .header-block { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
    .student-name { font-size: 24px; font-weight: 600; margin-bottom: 4px; }
    .student-contact { color: var(--text-secondary); font-size: 14px; }
    .student-contact i { width: 16px; text-align: center; }
    
    .data-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .data-label { font-size: 12px; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 4px; font-weight: 600; }
    .data-value { font-size: 14px; font-weight: 500; color: var(--text-primary); }
    
    .timeline-container { margin: 32px 0 24px 0; }
    .progress-track { height: 6px; background: var(--border-default); border-radius: 3px; position: relative; margin-bottom: 24px; }
    .progress-fill { height: 100%; background: var(--success); border-radius: 3px; transition: width 0.5s ease; }
    
    .timeline-steps { display: flex; flex-direction: column; gap: 12px; }
    .step { display: flex; align-items: center; gap: 12px; }
    .step-circle { width: 20px; height: 20px; border-radius: 50%; border: 2px solid var(--border-default); background: var(--bg-surface); display: flex; justify-content: center; align-items: center; font-size: 10px; color: transparent; }
    .step.active .step-circle { border-color: var(--primary); background: var(--primary); color: white; }
    .step.rejected .step-circle { border-color: var(--danger); background: var(--danger); color: white; }
    .step-text { font-size: 14px; color: var(--text-secondary); }
    .step.active .step-text { font-weight: 600; color: var(--text-primary); }
    .step.rejected .step-text { font-weight: 600; color: var(--danger); }
    
    .doc-list { display: flex; flex-direction: column; gap: 8px; margin-top: 16px; }
    .doc-item { display: flex; justify-content: space-between; align-items: center; padding: 12px; background: var(--bg-body); border: 1px solid var(--border-subtle); border-radius: var(--border-radius); }
    .doc-item a { color: var(--primary); text-decoration: none; font-weight: 500; display: flex; align-items: center; gap: 8px; }
    .doc-item a:hover { text-decoration: underline; }
    
    .notes-list { max-height: 400px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px; padding-right: 8px; }
    .note-card { background: var(--bg-body); padding: 12px; border-radius: var(--border-radius); border-left: 3px solid var(--primary); }
    .note-header { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 8px; }
    .note-author { font-weight: 600; color: var(--text-primary); }
    .note-date { color: var(--text-tertiary); }
    .note-text { font-size: 14px; line-height: 1.5; white-space: pre-wrap; color: var(--text-secondary); }
    
    @media (max-width: 900px) {
        .profile-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="profile-grid">
    <div class="main-column">
        <div class="card">
            <div class="header-block">
                <div>
                    <div class="student-name"><?php echo htmlspecialchars($student['full_name']); ?></div>
                    <div class="student-contact">
                        <div><i class="far fa-envelope"></i> <?php echo htmlspecialchars($student['email']); ?></div>
                        <div style="margin-top: 4px;"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($student['phone_number']); ?></div>
                    </div>
                </div>
                <div>
                    <a href="<?php echo $wa_link; ?>" target="_blank" class="btn" style="background: #25D366; color: white;">
                        <i class="fab fa-whatsapp"></i> Chat
                    </a>
                </div>
            </div>
            
            <div class="data-grid">
                <div><div class="data-label">Qualification</div><div class="data-value"><?php echo htmlspecialchars($student['highest_education']); ?></div></div>
                <div><div class="data-label">Result / Score</div><div class="data-value"><?php echo htmlspecialchars($student['result_score']); ?></div></div>
                <div><div class="data-label">Passing Year</div><div class="data-value"><?php echo htmlspecialchars($student['passing_year']); ?></div></div>
                <div><div class="data-label">Interested Field</div><div class="data-value"><?php echo htmlspecialchars($student['interested_to_study']); ?></div></div>
                <div><div class="data-label">Passport</div><div class="data-value"><?php echo htmlspecialchars($student['has_passport']); ?></div></div>
                <div><div class="data-label">Budget</div><div class="data-value"><?php echo htmlspecialchars($student['budget']); ?></div></div>
                <div><div class="data-label">Applied On</div><div class="data-value"><?php echo date('M j, Y', strtotime($student['created_at'])); ?></div></div>
                <div>
                    <div class="data-label">Assigned To</div>
                    <div class="data-value">
                        <?php if($student['assigned_to_name']): ?>
                            <span class="badge badge-success"><?php echo htmlspecialchars($student['assigned_to_name']); ?></span>
                        <?php else: ?>
                            <span class="badge badge-danger">Unassigned</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-title"><i class="fas fa-tasks"></i> Processing Timeline</div>
            
            <form method="POST" style="display:flex; gap:16px; align-items: center; margin-bottom: 24px;">
                <select name="processing_status" style="flex:1;">
                    <?php foreach($timeline_stages as $stage): ?>
                        <option value="<?php echo htmlspecialchars($stage); ?>" <?php if($student['processing_status'] === $stage) echo 'selected'; ?>><?php echo htmlspecialchars($stage); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
            </form>
            
            <div class="timeline-container">
                <div class="progress-track">
                    <div class="progress-fill" style="width: <?php echo $progress_percent; ?>%; <?php if(strpos($student['processing_status'], 'Rejected') !== false) echo 'background: var(--danger);'; ?>"></div>
                </div>
                <div class="timeline-steps">
                    <?php 
                    $reached_current = false;
                    foreach($timeline_stages as $idx => $stage): 
                        $is_current = ($stage === $student['processing_status']);
                        if ($is_current) $reached_current = true;
                        $is_rejected_stage = (strpos($stage, 'Rejected') !== false);
                        
                        $status_class = '';
                        if ($is_current) {
                            $status_class = $is_rejected_stage ? 'active rejected' : 'active';
                        } elseif (!$reached_current && !$is_rejected_stage) {
                            $status_class = 'active'; 
                        }
                        
                        if (strpos($student['processing_status'], 'Rejected') !== false) {
                            $status_class = $is_current ? 'active rejected' : '';
                        }
                    ?>
                        <div class="step <?php echo $status_class; ?>">
                            <div class="step-circle"><i class="fas fa-check"></i></div>
                            <div class="step-text"><?php echo htmlspecialchars($stage); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-title"><i class="fas fa-file-alt"></i> Documents</div>
            <form action="api_upload.php" method="POST" enctype="multipart/form-data" style="display:flex; gap:16px; align-items: center;">
                <input type="hidden" name="consultation_id" value="<?php echo $id; ?>">
                <input type="file" name="document" required style="flex:1; border: 1px dashed var(--border-default); padding: 8px; border-radius: var(--border-radius); background: var(--bg-body);">
                <button type="submit" class="btn btn-default">Upload</button>
            </form>
            
            <div class="doc-list">
                <?php if(empty($documents)): ?>
                    <div style="padding: 16px; text-align: center; color: var(--text-tertiary); border: 1px dashed var(--border-default); border-radius: var(--border-radius); margin-top: 16px;">
                        No documents uploaded yet.
                    </div>
                <?php else: ?>
                    <?php foreach($documents as $doc): ?>
                        <div class="doc-item">
                            <a href="<?php echo htmlspecialchars($doc['file_path']); ?>" target="_blank"><i class="fas fa-paperclip"></i> <?php echo htmlspecialchars($doc['file_name']); ?></a>
                            <span style="font-size: 12px; color: var(--text-tertiary);"><?php echo date('M j, Y', strtotime($doc['uploaded_at'])); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="side-column">
        <div class="card" style="height: 100%; display: flex; flex-direction: column;">
            <div class="card-title"><i class="fas fa-comments"></i> Internal Notes</div>
            
            <div class="notes-list" style="flex: 1;">
                <?php if(empty($notes)): ?>
                    <div style="text-align: center; color: var(--text-tertiary); margin-top: 20px;">No notes yet.</div>
                <?php else: ?>
                    <?php foreach($notes as $note): ?>
                        <div class="note-card">
                            <div class="note-header">
                                <span class="note-author"><?php echo htmlspecialchars($note['username']); ?></span>
                                <span class="note-date"><?php echo date('M j, g:i A', strtotime($note['created_at'])); ?></span>
                            </div>
                            <div class="note-text"><?php echo htmlspecialchars($note['note_text']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <form action="api_notes.php" method="POST" style="margin-top: 16px; border-top: 1px solid var(--border-subtle); padding-top: 16px;">
                <input type="hidden" name="consultation_id" value="<?php echo $id; ?>">
                <textarea name="note_text" rows="3" placeholder="Add a new note..." required style="margin-bottom: 12px; resize: vertical;"></textarea>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Post Note</button>
            </form>
        </div>
    </div>
</div>

<?php include 'layout_bottom.php'; ?>



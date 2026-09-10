<?php
// admin_portal/layout_bottom.php
?>
        </main>
    </div>

    <script>
        // Sidebar Toggle Logic
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuToggle = document.getElementById('menuToggle');

        function toggleSidebar() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }

        if (menuToggle) menuToggle.addEventListener('click', toggleSidebar);
        if (overlay) overlay.addEventListener('click', toggleSidebar);

        // Update Page Title based on a specific hidden input or default
        const pageTitleElement = document.getElementById('topPageTitle');
        const customTitle = document.getElementById('customPageTitle');
        if (pageTitleElement && customTitle) {
            pageTitleElement.innerText = customTitle.value;
        }
    </script>
</body>
</html>


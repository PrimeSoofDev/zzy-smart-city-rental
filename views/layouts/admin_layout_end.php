        </main>
    </div>

    <!-- JS for Interactivity -->
    <script>
        const menuToggle = document.getElementById('menuToggle');
        const closeSidebar = document.getElementById('closeSidebar');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const desktopCollapse = document.getElementById('desktopCollapse');
        const mainContent = document.getElementById('mainContent');
        const collapseIcon = document.getElementById('collapseIcon');

        // Check for saved state
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('sidebar-collapsed');
            mainContent.classList.add('content-expanded');
            if (collapseIcon) {
                collapseIcon.classList.replace('fa-indent', 'fa-outdent');
            }
        }

        if (desktopCollapse) {
            desktopCollapse.addEventListener('click', () => {
                const isCollapsed = sidebar.classList.toggle('sidebar-collapsed');
                mainContent.classList.toggle('content-expanded');
                
                // Update icon
                if (isCollapsed) {
                    collapseIcon.classList.replace('fa-indent', 'fa-outdent');
                } else {
                    collapseIcon.classList.replace('fa-outdent', 'fa-indent');
                }
                
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            });
        }

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        }

        menuToggle?.addEventListener('click', toggleSidebar);
        closeSidebar?.addEventListener('click', toggleSidebar);
        sidebarOverlay?.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>

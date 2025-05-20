document.addEventListener('DOMContentLoaded', function() {
    // Mostrar mensajes de error/éxito
    function showNotification(message, isError = true) {
        const notification = document.createElement('div');
        notification.className = `notification ${isError ? 'error' : 'success'}`;
        notification.textContent = message;
        
        const notifications = document.getElementById('notifications');
        notifications.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Mostrar mensajes de la URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error')) {
        showNotification(urlParams.get('error'));
    }
    if (urlParams.has('success')) {
        showNotification(urlParams.get('success'), false);
    }

    // Navegación del dashboard
    const menuItems = document.querySelectorAll('.menu-item');
    const contentSections = document.querySelectorAll('.content-section');
    
    if (menuItems.length > 0) {
        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                menuItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                
                contentSections.forEach(section => {
                    section.classList.remove('active');
                });
                
                document.getElementById(this.getAttribute('data-section')).classList.add('active');
            });
        });
    }

    // Cargar datos del usuario
    async function loadUserData() {
        try {
            const response = await fetch('php/get_user_data.php');
            const userData = await response.json();
            
            document.getElementById('user-name').textContent = `${userData.nombre} ${userData.apellido}`;
            document.getElementById('user-email').textContent = userData.email;
            
            // Llenar sección de datos
            document.getElementById('data-nombre').textContent = userData.nombre;
            document.getElementById('data-apellido').textContent = userData.apellido;
            document.getElementById('data-telefono').textContent = userData.telefono;
            document.getElementById('data-email').textContent = userData.email;
            document.getElementById('data-localidad').textContent = userData.localidad;
            document.getElementById('data-calle').textContent = userData.calle;
            document.getElementById('data-codigo-postal').textContent = userData.codigo_postal;
            
            // Llenar formulario de modificación
            document.getElementById('update-nombre').value = userData.nombre;
            document.getElementById('update-apellido').value = userData.apellido;
            document.getElementById('update-telefono').value = userData.telefono;
            document.getElementById('update-localidad').value = userData.localidad;
            document.getElementById('update-calle').value = userData.calle;
            document.getElementById('update-codigo_postal').value = userData.codigo_postal;
        } catch (error) {
            showNotification('Error al cargar datos del usuario');
        }
    }

    // Cargar citas
    async function loadCitas() {
        try {
            const response = await fetch('php/get_citas.php');
            const citas = await response.json();
            
            // Mostrar citas próximas
            const proximasContainer = document.getElementById('proximas-citas');
            if (proximasContainer) {
                proximasContainer.innerHTML = citas.proximas.length ? 
                    citas.proximas.map(cita => `
                        <div class="cita-item">
                            <p><strong>Fecha:</strong> ${cita.fecha}</p>
                            <p><strong>Hora:</strong> ${cita.hora}</p>
                            <p><strong>Motivo:</strong> ${cita.motivo}</p>
                            <button class="btn btn-small btn-cancel" data-id="${cita.id}">Cancelar</button>
                        </div>
                    `).join('') : '<p>No tienes citas próximas</p>';
            }
            
            // Mostrar citas anteriores
            const anterioresContainer = document.getElementById('anteriores-citas');
            if (anterioresContainer) {
                anterioresContainer.innerHTML = citas.anteriores.length ? 
                    citas.anteriores.map(cita => `
                        <div class="cita-item">
                            <p><strong>Fecha:</strong> ${cita.fecha}</p>
                            <p><strong>Hora:</strong> ${cita.hora}</p>
                            <p><strong>Motivo:</strong> ${cita.motivo}</p>
                        </div>
                    `).join('') : '<p>No tienes citas anteriores</p>';
            }
            
            // Eventos para cancelar citas
            document.querySelectorAll('.btn-cancel').forEach(btn => {
                btn.addEventListener('click', async function() {
                    try {
                        const response = await fetch('php/cancel_cita.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id=${this.getAttribute('data-id')}`
                        });
                        
                        if (response.ok) {
                            showNotification('Cita cancelada', false);
                            loadCitas();
                        } else {
                            showNotification('Error al cancelar cita');
                        }
                    } catch (error) {
                        showNotification('Error al cancelar cita');
                    }
                });
            });
        } catch (error) {
            showNotification('Error al cargar citas');
        }
    }

    // Enviar nueva cita
    document.getElementById('cita-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        try {
            const response = await fetch('php/create_cita.php', {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                showNotification('Cita creada', false);
                this.reset();
                loadCitas();
            } else {
                showNotification('Error al crear cita');
            }
        } catch (error) {
            showNotification('Error al crear cita');
        }
    });

    // Actualizar datos de usuario
    document.getElementById('update-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        try {
            const response = await fetch('php/update_user.php', {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                showNotification('Datos actualizados', false);
                loadUserData();
            } else {
                showNotification('Error al actualizar datos');
            }
        } catch (error) {
            showNotification('Error al actualizar datos');
        }
    });

    // Cerrar sesión
    document.getElementById('logout')?.addEventListener('click', async function(e) {
        e.preventDefault();
        try {
            await fetch('php/logout.php');
            window.location.href = 'login.html';
        } catch (error) {
            showNotification('Error al cerrar sesión');
        }
    });

    // Inicializar dashboard si estamos en esa página
    if (document.getElementById('user-menu')) {
        loadUserData();
        loadCitas();
    }
});
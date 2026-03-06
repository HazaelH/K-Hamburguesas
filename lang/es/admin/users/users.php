<?php
return [
    // Generales
    'back' => 'Volver',
    'cancel' => 'Cancelar',
    'confirm' => 'Confirmar',
    
    // Vista: Index
    'title_index' => 'Gestión de Usuarios',
    'subtitle_index' => 'Administra a tu equipo de trabajo y clientes registrados',
    'btn_new_user' => 'Nuevo Usuario',
    'stats_total' => 'Total Usuarios',
    'stats_admins' => 'Administradores',
    'stats_clients' => 'Clientes Activos',
    
    // Filtros
    'search_placeholder' => 'Buscar por nombre o correo electrónico...',
    'all_roles' => 'Todos los Roles',
    'role_admins' => 'Administradores',
    'role_clients' => 'Clientes',
    'role_staff' => 'Staff de Trabajo',
    'active_users' => 'Usuarios Activos',
    'trashed_users' => 'Ver Bajas (Papelera)',
    'btn_filter' => 'Filtrar',
    'clear_filters' => 'Limpiar Filtros',
    
    // Tabla
    'col_photo' => 'Foto',
    'col_info' => 'Información',
    'col_role' => 'Rol Asignado',
    'col_status' => 'Estado',
    'col_actions' => 'Acciones',
    'status_active' => 'Activo',
    'status_trashed' => 'Baja',
    'empty_title' => 'No hay usuarios',
    'empty_desc' => 'No encontramos resultados con esos filtros.',
    
    // Vista: Create & Edit
    'title_create' => 'Nuevo Usuario',
    'title_edit' => 'Editar Usuario',
    'label_name' => 'Nombre Completo',
    'placeholder_name' => 'Ej: Juan Pérez',
    'label_email' => 'Correo Electrónico',
    'placeholder_email' => 'correo@ejemplo.com',
    'label_role' => 'Rol',
    'role_cliente' => 'Cliente',
    'role_mesero' => 'Mesero',
    'role_cajero' => 'Cajero',
    'role_cocinero' => 'Cocinero / Preparador',
    'role_repartidor' => 'Repartidor',
    'role_admin' => 'Administrador',
    'label_password' => 'Contraseña',
    'placeholder_password_create' => 'Min 6 caracteres',
    'placeholder_password_edit' => 'Vacío para mantener',
    'label_password_confirmation' => 'Confirmar Contraseña',
    'placeholder_password_confirm' => 'Repite la contraseña',
    
    // Avatar
    'profile_image' => 'Imagen de Perfil',
    'preview_title' => 'Previsualización',
    'preview_desc' => 'Así se verá tu perfil',
    'choose_character' => 'Elige un personaje',
    'or_upload' => 'O sube una foto',
    'upload_formats' => 'Formatos: JPG, PNG, WEBP (Max 2MB)',
    
    // Botones Formulario
    'btn_save_user' => 'Guardar Usuario',
    'btn_update_user' => 'Actualizar Datos',

    // JS Modales (users-index.js)
    'modal_confirm_title' => '¿Confirmar Acción?',
    'modal_confirm_desc' => '¿Estás seguro de que deseas proceder?',
    
    'modal_soft_title' => '¿Dar de Baja?',
    'modal_soft_desc' => 'El usuario no podrá acceder, pero conservará su historial.',
    'btn_soft_delete' => 'Sí, dar de baja',
    
    'modal_hard_title' => '¿Eliminar Definitivo?',
    'modal_hard_desc' => 'Se borrará por completo de la base de datos. Irreversible.',
    'btn_hard_delete' => 'Destruir cuenta',
    
    // JS Toast (delete-users.js)
    'toast_success_title' => 'Operación Exitosa',
    'toast_error_title' => '¡Atención!',
    'toast_success_banner' => '¡Éxito!',
    'toast_error_banner' => 'Acción Denegada',
];
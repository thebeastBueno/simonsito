<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Simonsito DB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Estilos CSS integrados */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #e0f2fe; /* blue-50 - Azul muy claro para el fondo */
            font-size: 1.15rem; /* Aumentar el tamaño de fuente base */
        }
        /* Estilos para el botón de pestaña activo en el menú lateral */
        .tab-button.active {
            background-color: #1e40af; /* blue-800 oscuro */
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .tab-content {
            padding: 1.5rem;
            border-radius: 0.5rem;
            background-color: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .modal {
            z-index: 1000;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Estilos para tablas */
        .table-container {
            overflow-x: auto; /* Permite desplazamiento horizontal en tablas grandes */
            border-radius: 0.5rem; /* Bordes redondeados para el contenedor de la tabla */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* Sombra para la tabla */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0; /* Ya no es necesario margen superior si el contenedor tiene padding */
            font-size: 1.05rem; /* Ajustar tamaño de fuente para tablas */
        }
        th, td {
            padding: 0.8rem; /* Aumentar padding en celdas */
            text-align: left;
            border-bottom: 1px solid #bfdbfe; /* blue-200 - Borde de tabla más suave */
        }
        th {
            background-color: #dbeafe; /* blue-100 - Fondo de encabezado de tabla */
            font-weight: 700; /* Más resaltado para encabezados */
            color: #1e40af; /* blue-800 - Texto de encabezado de tabla */
            text-transform: uppercase;
            font-size: 0.95rem; /* Un poco más grande que antes */
        }
        tr:hover {
            background-color: #eff6ff; /* blue-50 - Fondo de fila al pasar el ratón */
        }
        /* Estilos específicos para botones de acción en tablas */
        .action-buttons button {
            margin-right: 0.5rem;
            padding: 0.6rem 1.1rem; /* Aumentar padding */
            font-size: 1rem; /* Aumentar tamaño de fuente */
            border-radius: 0.375rem;
            transition: background-color 0.2s ease-in-out;
            font-weight: 600; /* Hacerlos más resaltados */
        }
        .action-buttons .edit-btn {
            background-color: #3b82f6; /* blue-500 */
            color: white;
        }
        .action-buttons .edit-btn:hover {
            background-color: #2563eb; /* blue-600 */
        }
        .action-buttons .delete-btn {
            background-color: #ef4444; /* red-500 */
            color: white;
        }
        .action-buttons .delete-btn:hover {
            background-color: #dc2626; /* red-600 */
        }

        /* Ajustes para elementos de formulario */
        .form-label {
            font-size: 1.05rem; /* Aumentar tamaño de fuente para etiquetas de formulario */
            font-weight: 600; /* Hacerlas más resaltadas */
            color: #4a5568; /* gray-700 */
        }
        .form-input, .form-select, .form-textarea {
            padding: 0.7rem 1.1rem; /* Aumentar padding */
            font-size: 1.05rem; /* Aumentar tamaño de fuente */
        }

        /* Estilos para el contenedor principal de la aplicación cuando el login está oculto */
        .app-container.hidden {
            display: none;
        }
        /* Estilos para el contenedor del login/registro */
        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #e0f2fe; /* blue-50 */
        }
        .auth-card {
            background-color: #ffffff;
            padding: 2.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .auth-input {
            width: 100%;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid #d1d5db; /* gray-300 */
            border-radius: 0.375rem;
            font-size: 1rem;
            transition: all 0.2s ease-in-out;
        }
        .auth-input:focus {
            outline: none;
            border-color: #3b82f6; /* blue-500 */
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .auth-button {
            width: 100%;
            padding: 0.75rem;
            background-color: #2563eb; /* blue-600 */
            color: white;
            font-weight: 600;
            border-radius: 0.375rem;
            transition: background-color 0.2s ease-in-out;
        }
        .auth-button:hover {
            background-color: #1e40af; /* blue-800 */
        }
        .auth-error {
            color: #ef4444; /* red-500 */
            margin-top: 0.75rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .toggle-auth-mode {
            color: #2563eb;
            cursor: pointer;
            text-decoration: underline;
            margin-top: 1rem;
            display: block;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Contenedor del Login/Registro -->
    <div id="authApp" class="auth-container">
        <div class="auth-card">
            <img src="simon.png" alt="Simonsito Logo" class="w-24 h-24 mx-auto mb-6 object-cover shadow-md rounded-full">
            <h2 id="authTitle" class="text-3xl font-bold text-gray-800 mb-4">Iniciar Sesión</h2>
            
            <!-- Formulario de Login -->
            <form id="loginForm">
                <select id="userTypeLogin" class="auth-input mb-4">
                    <option value="">Selecciona tipo de usuario</option>
                    <option value="administrador">administrador</option>
                    <option value="representante">representante</option>
                </select>
                <input type="text" id="usernameLogin" placeholder="Usuario" class="auth-input" required>
                <input type="password" id="passwordLogin" placeholder="Contraseña" class="auth-input" required>
                <button type="submit" class="auth-button">Acceder</button>
                <p id="loginError" class="auth-error hidden"></p>
                <!-- El enlace de registro público se ha eliminado, ya que el registro es ahora solo para administradores -->
            </form>
        </div>
    </div>

    <!-- Contenedor Principal de la Aplicación (Oculto inicialmente) -->
    <div id="mainApp" class="flex min-h-screen hidden">
        <aside class="w-64 bg-blue-900 text-white p-6 flex flex-col items-center shadow-lg">
            <div class="text-center mb-8">
                <img src="simon.png" alt="Simonsito Logo" class="w-24 h-24 mx-auto mb-3 object-cover shadow-md">
                <h1 class="text-3xl font-bold text-blue-50">C.E.I SIMONCITO</h1>
                <p class="text-base text-blue-200 font-semibold">Gestión Escolar</p>
            </div>
            <nav class="w-full flex-grow">
                <button class="tab-button w-full text-left py-3 px-4 rounded-lg mb-2 transition-all duration-200 hover:bg-blue-700 active text-lg font-semibold" data-tab="estudiantes" data-roles="administrador,representante">
                    Estudiantes
                </button>
                <button class="tab-button w-full text-left py-3 px-4 rounded-lg mb-2 transition-all duration-200 hover:bg-blue-700 text-lg font-semibold" data-tab="niveles_grupos" data-roles="administrador">
                    Niveles y Grupos
                </button>
                <button class="tab-button w-full text-left py-3 px-4 rounded-lg mb-2 transition-all duration-200 hover:bg-blue-700 text-lg font-semibold" data-tab="madres" data-roles="administrador,representante">
                    Madres
                </button>
                <button class="tab-button w-full text-left py-3 px-4 rounded-lg mb-2 transition-all duration-200 hover:bg-blue-700 text-lg font-semibold" data-tab="padres" data-roles="administrador,representante">
                    Padres
                </button>
                <button class="tab-button w-full text-left py-3 px-4 rounded-lg mb-2 transition-all duration-200 hover:bg-blue-700 text-lg font-semibold" data-tab="representantes" data-roles="administrador,representante">
                    Representantes
                </button>
                <button class="tab-button w-full text-left py-3 px-4 rounded-lg mb-2 transition-all duration-200 hover:bg-blue-700 text-lg font-semibold" data-tab="inscripciones" data-roles="administrador">
                    Inscripciones
                </button>
                <button class="tab-button w-full text-left py-3 px-4 rounded-lg mb-2 transition-all duration-200 hover:bg-blue-700 text-lg font-semibold" data-tab="gestion_usuarios" data-roles="administrador">
                    Gestión de Usuarios
                </button>
                <button class="tab-button w-full text-left py-3 px-4 rounded-lg mb-2 transition-all duration-200 hover:bg-blue-700 text-lg font-semibold" data-tab="backup" data-roles="administrador">
                    Copia de Seguridad
                </button>
                <button class="tab-button w-full text-left py-3 px-4 rounded-lg mb-2 transition-all duration-200 hover:bg-blue-700 text-lg font-semibold" data-tab="estadisticas" data-roles="administrador">
                    Estadísticas
                </button>
            </nav>
            <button id="logoutBtn" class="w-full text-left py-3 px-4 rounded-lg mt-8 bg-red-700 hover:bg-red-800 transition-all duration-200 text-lg font-semibold">
                Cerrar Sesión
            </button>
        </aside>

        <main class="flex-grow p-8 bg-blue-50">
            <div id="tabContent_estudiantes" class="tab-content active">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-6 border-b-2 border-blue-500 pb-2">Gestión de Estudiantes</h2>
                
                <form id="estudianteForm" class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-blue-50 p-6 rounded-lg shadow-inner mb-8">
                    <input type="hidden" id="estudianteId" name="id">
                    
                    <div>
                        <div class="mb-4">
                            <label for="nombre_completo" class="block text-sm font-medium text-gray-700 mb-1 form-label">Nombre Completo</label>
                            <input type="text" id="nombre_completo" name="nombre_completo" required 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                          focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                        </div>
                        <div class="mb-4">
                            <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700 mb-1 form-label">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                          focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                        </div>
                        <div class="mb-4">
                            <label for="lugar_nacimiento" class="block text-sm font-medium text-gray-700 mb-1 form-label">Lugar de Nacimiento</label>
                            <input type="text" id="lugar_nacimiento" name="lugar_nacimiento"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                          focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                        </div>
                        <div class="mb-4">
                            <label for="entidad_federal" class="block text-sm font-medium text-gray-700 mb-1 form-label">Entidad Federal</label>
                            <input type="text" id="entidad_federal" name="entidad_federal"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                          focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                        </div>
                        <div class="mb-4">
                            <label for="nacionalidad_nino" class="block text-sm font-medium text-gray-700 mb-1 form-label">Nacionalidad</label>
                            <input type="text" id="nacionalidad_nino" name="nacionalidad_nino"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                          focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                        </div>
                    </div>

                    <div>
                        <div class="mb-4">
                            <label for="nivel_grupo_id" class="block text-sm font-medium text-gray-700 mb-1 form-label">Nivel/Grupo</label>
                            <select id="nivel_grupo_id" name="nivel_grupo_id"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 
                                           focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md form-select">
                                <option value="">Selecciona un Nivel/Grupo</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="madre_id" class="block text-sm font-medium text-gray-700 mb-1 form-label">Madre</label>
                            <select id="madre_id" name="madre_id"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 
                                           focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md form-select">
                                <option value="">Selecciona una Madre</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="padre_id" class="block text-sm font-medium text-gray-700 mb-1 form-label">Padre</label>
                            <select id="padre_id" name="padre_id"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 
                                           focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md form-select">
                                <option value="">Selecciona un Padre</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="representante_id" class="block text-sm font-medium text-gray-700 mb-1 form-label">Representante</label>
                            <select id="representante_id" name="representante_id"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 
                                           focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md form-select">
                                <option value="">Selecciona un Representante</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="condiciones_medicas" class="block text-sm font-medium text-gray-700 mb-1 form-label">Condiciones Médicas</label>
                            <textarea id="condiciones_medicas" name="condiciones_medicas" rows="3"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                             focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-y form-textarea"></textarea>
                        </div>
                    </div>

                    <div class="md:col-span-2 flex justify-end space-x-3 mt-4">
                        <button type="submit" 
                                class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-md 
                                       shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 
                                       focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out text-lg">
                            Guardar Estudiante
                        </button>
                        <button type="button" id="cancelarEstudianteBtn" 
                                class="px-8 py-3 bg-gray-300 text-gray-800 font-semibold rounded-md 
                                       shadow-md hover:bg-gray-400 focus:outline-none focus:ring-2 
                                       focus:ring-offset-2 focus:ring-gray-500 transition duration-150 ease-in-out text-lg">
                            Cancelar
                        </button>
                    </div>
                </form>

                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
                    <input type="text" id="searchInput" placeholder="Buscar estudiantes..." 
                           class="w-full sm:w-auto flex-grow px-4 py-3 border border-gray-300 rounded-md shadow-sm 
                                  focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-lg">
                    <div class="flex space-x-3">
                        <button id="exportEstudiantesPdfBtn" 
                                class="px-6 py-3 bg-red-600 text-white font-semibold rounded-md 
                                       shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 
                                       focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out flex items-center text-lg">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Exportar PDF
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <table id="estudiantesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre Completo</th>
                                <th>Fecha Nacimiento</th>
                                <th>Lugar Nacimiento</th>
                                <th>Nacionalidad</th>
                                <th>Nivel/Grupo</th>
                                <th>Madre</th>
                                <th>Padre</th>
                                <th>Representante</th>
                                <th>Condiciones Médicas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
                
                <div id="estudiantesPagination" class="flex justify-center mt-6 space-x-2">
                    </div>
            </div>

            <div id="tabContent_niveles_grupos" class="tab-content hidden">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-6 border-b-2 border-blue-500 pb-2">Gestión de Niveles y Grupos</h2>
                
                <form id="nivelGrupoForm" class="bg-blue-50 p-6 rounded-lg shadow-inner mb-8">
                    <input type="hidden" id="nivelGrupoId" name="id">
                    <div class="mb-4">
                        <label for="nombre_nivel_grupo" class="block text-sm font-medium text-gray-700 mb-1 form-label">Nombre Nivel/Grupo</label>
                        <input type="text" id="nombre_nivel_grupo" name="nombre_nivel_grupo" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                      focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                    </div>
                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-md shadow-md hover:bg-blue-700 transition duration-150 ease-in-out text-lg">Guardar</button>
                        <button type="button" id="cancelarNivelGrupoBtn" class="px-8 py-3 bg-gray-300 text-gray-800 font-semibold rounded-md shadow-md hover:bg-gray-400 transition duration-150 ease-in-out text-lg">Cancelar</button>
                    </div>
                </form>
                
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
                    <input type="text" id="searchNivelGrupo" placeholder="Buscar nivel o grupo..." 
                           class="w-full sm:w-auto flex-grow px-4 py-3 border border-gray-300 rounded-md shadow-sm 
                                  focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-lg">
                    <button id="exportNivelGrupoPdfBtn" 
                            class="px-6 py-3 bg-red-600 text-white font-semibold rounded-md 
                                   shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 
                                   focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out flex items-center text-lg">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Exportar PDF
                    </button>
                </div>

                <div class="table-container">
                    <table id="nivelesGruposTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre Nivel/Grupo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
                <div id="nivelesGruposPagination" class="flex justify-center mt-6 space-x-2">
                </div>
            </div>

            <div id="tabContent_madres" class="tab-content hidden">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-6 border-b-2 border-blue-500 pb-2">Gestión de Madres</h2>
                <form id="madreForm" class="bg-blue-50 p-6 rounded-lg shadow-inner mb-8">
                    <input type="hidden" id="madreId" name="id">
                    <div class="mb-4">
                        <label for="nombre_madre" class="block text-sm font-medium text-gray-700 mb-1 form-label">Nombre</label>
                        <input type="text" id="nombre_madre" name="nombre" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                    </div>
                    <div class="mb-4">
                        <label for="apellido_madre" class="block text-sm font-medium text-gray-700 mb-1 form-label">Apellido</label>
                        <input type="text" id="apellido_madre" name="apellido" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                    </div>
                    <div class="mb-4">
                        <label for="cedula_madre" class="block text-sm font-medium text-gray-700 mb-1 form-label">Cédula</label>
                        <input type="text" id="cedula_madre" name="cedula" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                    </div>
                    <div class="mb-4">
                        <label for="telefono_madre" class="block text-sm font-medium text-gray-700 mb-1 form-label">Teléfono</label>
                        <input type="tel" id="telefono_madre" name="telefono" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                    </div>
                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-md shadow-md hover:bg-blue-700 transition duration-150 ease-in-out text-lg">Guardar</button>
                        <button type="button" id="cancelarMadreBtn" class="px-8 py-3 bg-gray-300 text-gray-800 font-semibold rounded-md shadow-md hover:bg-gray-400 transition duration-150 ease-in-out text-lg">Cancelar</button>
                    </div>
                </form>
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
                    <input type="text" id="searchMadre" placeholder="Buscar madre..." 
                           class="w-full sm:w-auto flex-grow px-4 py-3 border border-gray-300 rounded-md shadow-sm 
                                  focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-lg">
                    <button id="exportMadresPdfBtn" 
                            class="px-6 py-3 bg-red-600 text-white font-semibold rounded-md 
                                   shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 
                                   focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out flex items-center text-lg">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Exportar PDF
                    </button>
                </div>
                <div class="table-container">
                    <table id="madresTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Cédula</th>
                                <th>Teléfono</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
                <div id="madresPagination" class="flex justify-center mt-6 space-x-2">
                </div>
            </div>

            <div id="tabContent_padres" class="tab-content hidden">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-6 border-b-2 border-blue-500 pb-2">Gestión de Padres</h2>
                <form id="padreForm" class="bg-blue-50 p-6 rounded-lg shadow-inner mb-8">
                    <input type="hidden" id="padreId" name="id">
                    <div class="mb-4">
                        <label for="nombre_padre" class="block text-sm font-medium text-gray-700 mb-1 form-label">Nombre</label>
                        <input type="text" id="nombre_padre" name="nombre" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                    </div>
                    <div class="mb-4">
                        <label for="apellido_padre" class="block text-sm font-medium text-gray-700 mb-1 form-label">Apellido</label>
                        <input type="text" id="apellido_padre" name="apellido" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                    </div>
                    <div class="mb-4">
                        <label for="cedula_padre" class="block text-sm font-medium text-gray-700 mb-1 form-label">Cédula</label>
                        <input type="text" id="cedula_padre" name="cedula" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                    </div>
                    <div class="mb-4">
                        <label for="telefono_padre" class="block text-sm font-medium text-gray-700 mb-1 form-label">Teléfono</label>
                        <input type="tel" id="telefono_padre" name="telefono" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                    </div>
                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-md shadow-md hover:bg-blue-700 transition duration-150 ease-in-out text-lg">Guardar</button>
                        <button type="button" id="cancelarPadreBtn" class="px-8 py-3 bg-gray-300 text-gray-800 font-semibold rounded-md shadow-md hover:bg-gray-400 transition duration-150 ease-in-out text-lg">Cancelar</button>
                    </div>
                </form>
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
                    <input type="text" id="searchPadre" placeholder="Buscar padre..." 
                           class="w-full sm:w-auto flex-grow px-4 py-3 border border-gray-300 rounded-md shadow-sm 
                                  focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-lg">
                    <button id="exportPadresPdfBtn" 
                            class="px-6 py-3 bg-red-600 text-white font-semibold rounded-md 
                                   shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 
                                   focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out flex items-center text-lg">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Exportar PDF
                    </button>
                </div>
                <div class="table-container">
                    <table id="padresTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Cédula</th>
                                <th>Teléfono</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
                <div id="padresPagination" class="flex justify-center mt-6 space-x-2">
                </div>
            </div>

            <div id="tabContent_representantes" class="tab-content hidden">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-6 border-b-2 border-blue-500 pb-2">Gestión de Representantes</h2>
                <form id="representanteForm" class="bg-blue-50 p-6 rounded-lg shadow-inner mb-8">
                    <input type="hidden" id="representanteId" name="id">
                    <div class="mb-4">
                        <label for="nombre_representante" class="block text-sm font-medium text-gray-700 mb-1 form-label">Nombre</label>
                        <input type="text" id="nombre_representante" name="nombre" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                    </div>
                    <div class="mb-4">
                        <label for="apellido_representante" class="block text-sm font-medium text-gray-700 mb-1 form-label">Apellido</label>
                        <input type="text" id="apellido_representante" name="apellido" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                    </div>
                    <div class="mb-4">
                        <label for="cedula_representante" class="block text-sm font-medium text-gray-700 mb-1 form-label">Cédula</label>
                        <input type="text" id="cedula_representante" name="cedula" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                    </div>
                    <div class="mb-4">
                        <label for="telefono_representante" class="block text-sm font-medium text-gray-700 mb-1 form-label">Teléfono</label>
                        <input type="tel" id="telefono_representante" name="telefono" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm form-input">
                    </div>
                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-md shadow-md hover:bg-blue-700 transition duration-150 ease-in-out text-lg">Guardar</button>
                        <button type="button" id="cancelarRepresentanteBtn" class="px-8 py-3 bg-gray-300 text-gray-800 font-semibold rounded-md shadow-md hover:bg-gray-400 transition duration-150 ease-in-out text-lg">Cancelar</button>
                    </div>
                </form>
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
                    <input type="text" id="searchRepresentante" placeholder="Buscar representante..." 
                           class="w-full sm:w-auto flex-grow px-4 py-3 border border-gray-300 rounded-md shadow-sm 
                                  focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-lg">
                    <button id="exportRepresentantesPdfBtn" 
                            class="px-6 py-3 bg-red-600 text-white font-semibold rounded-md 
                                   shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 
                                   focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out flex items-center text-lg">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Exportar PDF
                    </button>
                </div>
                <div class="table-container">
                    <table id="representantesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Cédula</th>
                                <th>Teléfono</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
                <div id="representantesPagination" class="flex justify-center mt-6 space-x-2">
                </div>
            </div>

            <div id="tabContent_inscripciones" class="tab-content hidden">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-6 border-b-2 border-blue-500 pb-2">Gestión de Inscripciones</h2>
                <form id="inscripcionForm" class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-blue-50 p-6 rounded-lg shadow-inner mb-8">
                    <input type="hidden" id="inscripcionId" name="id">
                    <div>
                        <div class="mb-4">
                            <label for="estudiante_id_inscripcion" class="block text-sm font-medium text-gray-700 mb-1 form-label">Estudiante</label>
                            <select id="estudiante_id_inscripcion" name="estudiante_id" required
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md form-select">
                                <option value="">Selecciona un estudiante</option>
                                </select>
                        </div>
                        <div class="mb-4">
                            <label for="nivel_grupo_id_inscripcion" class="block text-sm font-medium text-gray-700 mb-1 form-label">Nivel/Grupo</label>
                            <select id="nivel_grupo_id_inscripcion" name="nivel_grupo_id" required
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md form-select">
                                <option value="">Selecciona un nivel/grupo</option>
                                </select>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4">
                            <label for="madre_id_inscripcion" class="block text-sm font-medium text-gray-700 mb-1 form-label">Madre</label>
                            <select id="madre_id_inscripcion" name="madre_id"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md form-select">
                                <option value="">Ninguna</option>
                                </select>
                        </div>
                        <div class="mb-4">
                            <label for="padre_id_inscripcion" class="block text-sm font-medium text-gray-700 mb-1 form-label">Padre</label>
                            <select id="padre_id_inscripcion" name="padre_id"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md form-select">
                                <option value="">Ninguno</option>
                                </select>
                        </div>
                         <div class="mb-4">
                            <label for="representante_id_inscripcion" class="block text-sm font-medium text-gray-700 mb-1 form-label">Representante</label>
                            <select id="representante_id_inscripcion" name="representante_id" required
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md form-select">
                                <option value="">Selecciona un representante</option>
                                </select>
                        </div>
                    </div>
                    <div class="md:col-span-2 flex justify-end space-x-3 mt-4">
                        <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-md shadow-md hover:bg-blue-700 transition duration-150 ease-in-out text-lg">Guardar Inscripción</button>
                        <button type="button" id="cancelarInscripcionBtn" class="px-8 py-3 bg-gray-300 text-gray-800 font-semibold rounded-md shadow-md hover:bg-gray-400 transition duration-150 ease-in-out text-lg">Cancelar</button>
                    </div>
                </form>
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
                    <input type="text" id="searchInscripcion" placeholder="Buscar inscripción..." 
                           class="w-full sm:w-auto flex-grow px-4 py-3 border border-gray-300 rounded-md shadow-sm 
                                  focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-lg">
                    <button id="exportInscripcionesPdfBtn" 
                            class="px-6 py-3 bg-red-600 text-white font-semibold rounded-md 
                                   shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 
                                   focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out flex items-center text-lg">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Exportar PDF
                    </button>
                </div>
                <div class="table-container">
                    <table id="inscripcionesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Estudiante</th>
                                <th>Nivel/Grupo</th>
                                <th>Madre</th>
                                <th>Padre</th>
                                <th>Representante</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
                <div id="inscripcionesPagination" class="flex justify-center mt-6 space-x-2">
                </div>
            </div>

            <!-- Nueva Pestaña: Gestión de Usuarios -->
            <div id="tabContent_gestion_usuarios" class="tab-content hidden">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-6 border-b-2 border-blue-500 pb-2">Gestión de Usuarios</h2>
                <div class="bg-blue-50 p-6 rounded-lg shadow-inner mb-8">
                    <p class="text-xl text-gray-700 mb-6 text-center font-semibold">
                        Aquí puedes registrar nuevos administradores o representantes.
                    </p>
                    <form id="registerUserForm" class="grid grid-cols-1 gap-6">
                        <div class="mb-4">
                            <label for="newUsername" class="block text-sm font-medium text-gray-700 mb-1 form-label">Nombre de Usuario</label>
                            <input type="text" id="newUsername" placeholder="Nombre de usuario" class="auth-input" required>
                        </div>
                        <div class="mb-4">
                            <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-1 form-label">Contraseña</label>
                            <input type="password" id="newPassword" placeholder="Contraseña" class="auth-input" required>
                        </div>
                        <div class="mb-4">
                            <label for="confirmNewPassword" class="block text-sm font-medium text-gray-700 mb-1 form-label">Confirmar Contraseña</label>
                            <input type="password" id="confirmNewPassword" placeholder="Confirmar contraseña" class="auth-input" required>
                        </div>
                        <div class="mb-4">
                            <label for="newUserRole" class="block text-sm font-medium text-gray-700 mb-1 form-label">Rol</label>
                            <select id="newUserRole" class="auth-input" required>
                                <option value="">Selecciona un rol</option>
                                <option value="administrador">Administrador</option>
                                <option value="representante">Representante</option>
                            </select>
                        </div>
                        <p id="registerUserError" class="auth-error hidden"></p>
                        <div class="flex justify-end space-x-3 mt-4">
                            <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-md shadow-md hover:bg-blue-700 transition duration-150 ease-in-out text-lg">
                                Registrar Usuario
                            </button>
                            <button type="button" id="cancelRegisterUserBtn" class="px-8 py-3 bg-gray-300 text-gray-800 font-semibold rounded-md shadow-md hover:bg-gray-400 transition duration-150 ease-in-out text-lg">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Sección de Tabla de Usuarios -->
                <div class="mt-10">
                    <h3 class="text-3xl font-extrabold text-gray-900 mb-6 border-b-2 border-blue-500 pb-2">Usuarios Registrados</h3>
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
                        <input type="text" id="searchUsers" placeholder="Buscar usuarios..." 
                               class="w-full sm:w-auto flex-grow px-4 py-3 border border-gray-300 rounded-md shadow-sm 
                                      focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-lg">
                        <button id="exportUsersPdfBtn" 
                                class="px-6 py-3 bg-red-600 text-white font-semibold rounded-md 
                                       shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 
                                       focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out flex items-center text-lg">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Exportar PDF
                        </button>
                    </div>
                    <div class="table-container">
                        <table id="usersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los usuarios se cargarán aquí -->
                            </tbody>
                        </table>
                    </div>
                    <div id="usersPagination" class="flex justify-center mt-6 space-x-2">
                        </div>
                </div>

                <!-- Modal para editar usuario -->
                <div id="editUserModal" class="modal hidden">
                    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">Editar Usuario</h3>
                        <form id="editUserForm">
                            <input type="hidden" id="editUserId">
                            <div class="mb-4">
                                <label for="editUsername" class="block text-sm font-medium text-gray-700 mb-1 form-label">Nombre de Usuario</label>
                                <input type="text" id="editUsername" class="auth-input" required>
                            </div>
                            <div class="mb-4">
                                <label for="editUserPassword" class="block text-sm font-medium text-gray-700 mb-1 form-label">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                                <input type="password" id="editUserPassword" class="auth-input">
                            </div>
                            <div class="mb-4">
                                <label for="editUserRole" class="block text-sm font-medium text-gray-700 mb-1 form-label">Rol</label>
                                <select id="editUserRole" class="auth-input" required>
                                    <option value="administrador">Administrador</option>
                                    <option value="representante">Representante</option>
                                </select>
                            </div>
                            <p id="editUserError" class="auth-error hidden"></p>
                            <div class="flex justify-end space-x-3 mt-4">
                                <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-md shadow-md hover:bg-blue-700 transition duration-150 ease-in-out text-lg">Guardar Cambios</button>
                                <button type="button" id="cancelEditUserBtn" class="px-8 py-3 bg-gray-300 text-gray-800 font-semibold rounded-md shadow-md hover:bg-gray-400 transition duration-150 ease-in-out text-lg">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <div id="tabContent_backup" class="tab-content hidden">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-6 border-b-2 border-blue-500 pb-2">Copia de Seguridad de la Base de Datos</h2>
                <div class="flex flex-col items-center justify-center p-6 bg-blue-50 rounded-lg shadow-inner">
                    <p class="text-xl text-gray-700 mb-6 text-center font-semibold">Genera una copia de seguridad de la base de datos "simonsito_db".</p>
                    <button id="generarBackupBtn" 
                            class="px-10 py-4 bg-green-600 text-white font-bold rounded-lg shadow-md 
                                   hover:bg-green-700 focus:outline-none focus:ring-2 
                                   focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out flex items-center text-xl">
                        <svg class="w-7 h-7 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Generar Copia de Seguridad
                    </button>
                    <div id="backupStatus" class="mt-6 p-4 text-center text-base font-semibold rounded-md w-full max-w-md hidden">
                        </div>
                </div>
            </div>

            <div id="tabContent_estadisticas" class="tab-content hidden">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-6 border-b-2 border-blue-500 pb-2">Estadísticas</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4">Estudiantes por Nivel/Grupo</h3>
                        <canvas id="nivelGrupoChart"></canvas>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4">Inscripciones por Nivel/Grupo</h3>
                        <canvas id="inscripcionesChart"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const apiBaseUrl = 'api.php'; // URL base de tu API

            // Elementos del DOM para el login/registro y la aplicación principal
            const authApp = document.getElementById('authApp');
            const mainApp = document.getElementById('mainApp');
            const authTitle = document.getElementById('authTitle');
            
            const loginForm = document.getElementById('loginForm');
            const userTypeLoginSelect = document.getElementById('userTypeLogin');
            const usernameLoginInput = document.getElementById('usernameLogin');
            const passwordLoginInput = document.getElementById('passwordLogin');
            const loginError = document.getElementById('loginError');

            const logoutBtn = document.getElementById('logoutBtn');

            // Nuevos elementos para el formulario de registro de usuarios (solo para administradores)
            const registerUserForm = document.getElementById('registerUserForm');
            const newUsernameInput = document.getElementById('newUsername');
            const newPasswordInput = document.getElementById('newPassword');
            const confirmNewPasswordInput = document.getElementById('confirmNewPassword');
            const newUserRoleSelect = document.getElementById('newUserRole');
            const registerUserError = document.getElementById('registerUserError');
            const cancelRegisterUserBtn = document.getElementById('cancelRegisterUserBtn');

            // Elementos para la gestión de usuarios
            const usersTableBody = document.getElementById('usersTable').querySelector('tbody');
            const searchUsersInput = document.getElementById('searchUsers');
            const exportUsersPdfBtn = document.getElementById('exportUsersPdfBtn');
            const editUserModal = document.getElementById('editUserModal');
            const editUserForm = document.getElementById('editUserForm');
            const editUserIdInput = document.getElementById('editUserId');
            const editUsernameInput = document.getElementById('editUsername');
            const editUserPasswordInput = document.getElementById('editUserPassword');
            const editUserRoleSelect = document.getElementById('editUserRole');
            const editUserError = document.getElementById('editUserError');
            const cancelEditUserBtn = document.getElementById('cancelEditUserBtn');


            let currentUserRole = localStorage.getItem('userRole'); // Obtener el rol del almacenamiento local
            let currentActiveTab = localStorage.getItem('activeTab') || 'estudiantes'; // Obtener la pestaña activa

            // Arrays para almacenar datos
            let allEstudiantes = [];
            let allNivelesGrupos = [];
            let allMadres = [];
            let allPadres = [];
            let allRepresentantes = [];
            let allInscripciones = [];
            let allUsers = []; // Nuevo array para usuarios

            const rowsPerPage = 10;
            let currentPage = 1;

            // Funciones de fetch (existentes, no se modifican directamente aquí)
            async function fetchData(entity, id = null) {
                const url = id ? `${apiBaseUrl}?entity=${entity}&id=${id}` : `${apiBaseUrl}?entity=${entity}`;
                try {
                    const response = await fetch(url);
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error(`HTTP error! status: ${response.status}, response: ${errorText}`);
                        throw new Error(`HTTP error! status: ${response.status}, response: ${errorText}`);
                    }
                    const data = await response.json();
                    if (data && data.error) {
                        console.error(`Error al cargar ${entity}:`, data.error);
                        return null;
                    }
                    return data;
                } catch (error) {
                    console.error(`Error al obtener ${entity}:`, error);
                    return null;
                }
            }

            async function postData(entity, data) {
                try {
                    const response = await fetch(`${apiBaseUrl}?entity=${entity}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error(`HTTP error! status: ${response.status}, response: ${errorText}`);
                        throw new Error(`HTTP error! status: ${response.status}, response: ${errorText}`);
                    }
                    const result = await response.json();
                    if (result && result.error) {
                         console.error(`Error de la API al guardar ${entity}:`, result.error);
                         return { success: false, message: result.error };
                    }
                    return result;
                } catch (error) {
                    console.error(`Error al guardar ${entity}:`, error);
                    return { success: false, message: error.message };
                }
            }

            async function putData(entity, id, data) {
                try {
                    const response = await fetch(`${apiBaseUrl}?entity=${entity}&id=${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error(`HTTP error! status: ${response.status}, response: ${errorText}`);
                        throw new Error(`HTTP error! status: ${response.status}, response: ${errorText}`);
                    }
                    const result = await response.json();
                    if (result && result.error) {
                         console.error(`Error de la API al actualizar ${entity}:`, result.error);
                         return { success: false, message: result.error };
                    }
                    return result;
                } catch (error) {
                    console.error(`Error al actualizar ${entity}:`, error);
                    return { success: false, message: error.message };
                }
            }

            async function deleteData(entity, id) {
                try {
                    const response = await fetch(`${apiBaseUrl}?entity=${entity}&id=${id}`, {
                        method: 'DELETE'
                    });
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error(`HTTP error! status: ${response.status}, response: ${errorText}`);
                        throw new Error(`HTTP error! status: ${response.status}, response: ${errorText}`);
                    }
                    const result = await response.json();
                    if (result && result.error) {
                         console.error(`Error de la API al eliminar ${entity}:`, result.error);
                         return { success: false, message: result.error };
                    }
                    return result;
                } catch (error) {
                    console.error(`Error al eliminar ${entity}:`, error);
                    return { success: false, message: error.message };
                }
            }

            // --- Lógica de Autenticación y Registro ---
            function showLoginScreen() {
                authApp.classList.remove('hidden');
                mainApp.classList.add('hidden');
                localStorage.removeItem('userRole'); // Limpiar rol al mostrar login
                localStorage.removeItem('activeTab'); // Limpiar pestaña activa
                currentUserRole = null;
                loginForm.reset(); // Resetear el formulario de login
                loginError.classList.add('hidden'); // Ocultar errores
                authTitle.textContent = 'Iniciar Sesión';
            }

            function showMainApp(role) {
                authApp.classList.add('hidden');
                mainApp.classList.remove('hidden');
                currentUserRole = role;
                localStorage.setItem('userRole', role); // Guardar rol en el almacenamiento local
                applyRolePermissions(role);
                // Activar la pestaña guardada o la primera permitida
                activateTab(currentActiveTab);
            }

            // Función para aplicar permisos basados en el rol
            function applyRolePermissions(role) {
                const tabButtons = document.querySelectorAll('.tab-button');
                tabButtons.forEach(button => {
                    const rolesAllowed = button.dataset.roles ? button.dataset.roles.split(',') : [];
                    if (rolesAllowed.includes(role)) {
                        button.classList.remove('hidden'); // Mostrar el botón
                    } else {
                        button.classList.add('hidden'); // Ocultar el botón
                    }
                });

                // Asegurarse de que la pestaña activa sea accesible para el rol actual
                const activeTabButton = document.querySelector(`.tab-button[data-tab="${currentActiveTab}"]`);
                if (activeTabButton && activeTabButton.classList.contains('hidden')) {
                    // Si la pestaña activa no está permitida, redirigir a la primera pestaña permitida
                    const firstAllowedTab = document.querySelector(`.tab-button:not(.hidden)`);
                    if (firstAllowedTab) {
                        activateTab(firstAllowedTab.dataset.tab);
                    } else {
                        // Si no hay pestañas permitidas, mostrar un mensaje o redirigir al login
                        console.warn('No hay pestañas accesibles para este rol.');
                        showLoginScreen();
                    }
                }
            }

            // Manejar el cambio en el select de tipo de usuario (Login)
            userTypeLoginSelect.addEventListener('change', () => {
                const selectedType = userTypeLoginSelect.value;
                if (selectedType === 'administrador') {
                    usernameLoginInput.placeholder = "Ej: admin";
                } else if (selectedType === 'representante') {
                    usernameLoginInput.placeholder = "Ej: representante1";
                } else {
                    usernameLoginInput.placeholder = "Usuario";
                }
                passwordLoginInput.value = ''; // Limpiar la contraseña al cambiar el tipo de usuario
                loginError.classList.add('hidden'); // Ocultar errores
            });

            // Manejar el envío del formulario de login
            loginForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const username = usernameLoginInput.value;
                const password = passwordLoginInput.value;
                const userType = userTypeLoginSelect.value; // Para la UX, no se envía al API directamente

                loginError.classList.add('hidden'); // Ocultar errores anteriores

                if (!userType) {
                    loginError.textContent = 'Por favor, selecciona un tipo de usuario.';
                    loginError.classList.remove('hidden');
                    return;
                }

                try {
                    const response = await fetch(`${apiBaseUrl}?entity=login`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ username, password })
                    });
                    const result = await response.json();

                    if (result.success) {
                        showMainApp(result.role);
                    } else {
                        loginError.textContent = result.message || 'Credenciales inválidas.';
                        loginError.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error de conexión al intentar iniciar sesión:', error);
                    loginError.textContent = 'Error de conexión. Inténtalo de nuevo.';
                    loginError.classList.remove('hidden');
                }
            });

            // Manejar el envío del formulario de registro de usuarios (solo para administradores)
            registerUserForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const username = newUsernameInput.value;
                const password = newPasswordInput.value;
                const confirmPassword = confirmNewPasswordInput.value;
                const role = newUserRoleSelect.value;

                registerUserError.classList.add('hidden'); // Ocultar errores anteriores

                if (!role) {
                    registerUserError.textContent = 'Por favor, selecciona un rol para el nuevo usuario.';
                    registerUserError.classList.remove('hidden');
                    return;
                }

                if (password !== confirmPassword) {
                    registerUserError.textContent = 'Las contraseñas no coinciden.';
                    registerUserError.classList.remove('hidden');
                    return;
                }
                if (password.length < 6) {
                    registerUserError.textContent = 'La contraseña debe tener al menos 6 caracteres.';
                    registerUserError.classList.remove('hidden');
                    return;
                }

                try {
                    // Enviar la solicitud a la nueva entidad admin_register_user
                    const response = await fetch(`${apiBaseUrl}?entity=admin_register_user`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ username, password, role })
                    });
                    const result = await response.json();

                    if (result.success) {
                        showCustomAlert(result.message || 'Usuario registrado exitosamente.');
                        registerUserForm.reset(); // Limpiar el formulario
                        fetchUsers(); // Recargar la tabla de usuarios
                    } else {
                        registerUserError.textContent = result.message || 'Error en el registro del usuario.';
                        registerUserError.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error de conexión al intentar registrar usuario:', error);
                    registerUserError.textContent = 'Error de conexión. Inténtalo de nuevo.';
                    registerUserError.classList.remove('hidden');
                }
            });

            cancelRegisterUserBtn.addEventListener('click', () => {
                registerUserForm.reset();
                registerUserError.classList.add('hidden');
            });


            // Manejar el cierre de sesión
            logoutBtn.addEventListener('click', () => {
                showConfirmModal('¿Estás seguro de que quieres cerrar sesión?', async () => {
                    // Opcional: Si tu API tuviera un endpoint de logout para limpiar la sesión del servidor
                    // await fetch(`${apiBaseUrl}?entity=logout`, { method: 'POST' });
                    showLoginScreen();
                });
            });

            // --- Lógica para Estudiantes ---
            const estudianteForm = document.getElementById('estudianteForm');
            const estudiantesTableBody = document.getElementById('estudiantesTable').querySelector('tbody');
            const estudianteIdField = document.getElementById('estudianteId');
            const cancelarEstudianteBtn = document.getElementById('cancelarEstudianteBtn');
            const searchInput = document.getElementById('searchInput');
            const exportEstudiantesPdfBtn = document.getElementById('exportEstudiantesPdfBtn');

            async function fetchEstudiantes() {
                const estudiantes = await fetchData('estudiantes');
                allNivelesGrupos = await fetchData('niveles_grupos') || [];
                allMadres = await fetchData('madres') || [];
                allPadres = await fetchData('padres') || [];
                allRepresentantes = await fetchData('representantes') || [];

                if (estudiantes) {
                    allEstudiantes = estudiantes;
                    renderEstudiantesTable(allEstudiantes, currentPage);
                    renderPagination(allEstudiantes.length, 'estudiantesPagination', 'estudiantesTable');
                }
            }

            function renderEstudiantesTable(data, page) {
                estudiantesTableBody.innerHTML = '';
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const paginatedData = data.slice(start, end);

                paginatedData.forEach(estudiante => {
                    const nivelGrupo = allNivelesGrupos.find(ng => ng.id == estudiante.nivel_grupo_id);
                    const madre = allMadres.find(m => m.id == estudiante.madre_id);
                    const padre = allPadres.find(p => p.id == estudiante.padre_id);
                    const representante = allRepresentantes.find(r => r.id == estudiante.representante_id);

                    const row = estudiantesTableBody.insertRow();
                    row.className = 'hover:bg-gray-50'; // Añadir clase hover
                    row.innerHTML = `
                        <td>${estudiante.id}</td>
                        <td>${estudiante.nombre_completo || 'N/A'}</td>
                        <td>${estudiante.fecha_nacimiento || 'N/A'}</td>
                        <td>${estudiante.lugar_nacimiento || 'N/A'}</td>
                        <td>${estudiante.nacionalidad_nino || 'N/A'}</td>
                        <td>${nivelGrupo ? (nivelGrupo.nombre_nivel_grupo || 'N/A') : 'N/A'}</td>
                        <td>${madre ? `${madre.nombre || 'N/A'} ${(madre.apellido || '')}`.trim() : 'N/A'}</td>
                        <td>${padre ? `${padre.nombre || 'N/A'} ${(padre.apellido || '')}`.trim() : 'N/A'}</td>
                        <td>${representante ? `${representante.nombre || 'N/A'} ${(representante.apellido || '')}`.trim() : 'N/A'}</td>
                        <td>${estudiante.condiciones_medicas || 'N/A'}</td>
                        <td class="action-buttons">
                            <button class="edit-btn" data-id="${estudiante.id}" data-entity="estudiantes">Editar</button>
                            <button class="delete-btn" data-id="${estudiante.id}" data-entity="estudiantes">Eliminar</button>
                        </td>
                    `;
                });
            }

            function renderPagination(totalRows, paginationContainerId, tableId) {
                const paginationContainer = document.getElementById(paginationContainerId);
                paginationContainer.innerHTML = '';
                const totalPages = Math.ceil(totalRows / rowsPerPage);

                for (let i = 1; i <= totalPages; i++) {
                    const button = document.createElement('button');
                    button.textContent = i;
                    button.classList.add('px-3', 'py-1', 'rounded-md', 'border', 'border-gray-300', 'hover:bg-blue-100', 'transition', 'duration-150');
                    if (i === currentPage) {
                        button.classList.add('bg-blue-500', 'text-white', 'hover:bg-blue-600');
                    } else {
                        button.classList.add('bg-white', 'text-gray-700');
                    }
                    button.addEventListener('click', () => {
                        currentPage = i;
                        const currentSearch = document.getElementById(
                            tableId === 'estudiantesTable' ? 'searchInput' : 
                            (tableId === 'nivelesGruposTable' ? 'searchNivelGrupo' : 
                            (tableId === 'madresTable' ? 'searchMadre' : 
                            (tableId === 'padresTable' ? 'searchPadre' : 
                            (tableId === 'representantesTable' ? 'searchRepresentante' : 
                            (tableId === 'inscripcionesTable' ? 'searchInscripcion' : 'searchUsers')))))
                        ).value.toLowerCase();
                        let filteredData = [];
                        
                        if (tableId === 'estudiantesTable') {
                            filteredData = allEstudiantes.filter(e => 
                                (e.nombre_completo || '').toLowerCase().includes(currentSearch) ||
                                (e.lugar_nacimiento || '').toLowerCase().includes(currentSearch) ||
                                (e.nacionalidad_nino || '').toLowerCase().includes(currentSearch) ||
                                (allNivelesGrupos.find(ng => ng.id == e.nivel_grupo_id)?.nombre_nivel_grupo || '').toLowerCase().includes(currentSearch) ||
                                (`${allMadres.find(m => m.id == e.madre_id)?.nombre || ''} ${allMadres.find(m => m.id == e.madre_id)?.apellido || ''}`).toLowerCase().includes(currentSearch) ||
                                (`${allPadres.find(p => p.id == e.padre_id)?.nombre || ''} ${allPadres.find(p => p.id == e.padre_id)?.apellido || ''}`).toLowerCase().includes(currentSearch) ||
                                (`${allRepresentantes.find(r => r.id == e.representante_id)?.nombre || ''} ${allRepresentantes.find(r => r.id == e.representante_id)?.apellido || ''}`).toLowerCase().includes(currentSearch)
                            );
                        } else if (tableId === 'nivelesGruposTable') {
                            filteredData = allNivelesGrupos.filter(ng => (ng.nombre_nivel_grupo || '').toLowerCase().includes(currentSearch));
                        } else if (tableId === 'madresTable') {
                            filteredData = allMadres.filter(m => (m.nombre || '').toLowerCase().includes(currentSearch) || (m.apellido || '').toLowerCase().includes(currentSearch) || (m.cedula || '').toLowerCase().includes(currentSearch) || (m.telefono || '').toLowerCase().includes(currentSearch));
                        } else if (tableId === 'padresTable') {
                            filteredData = allPadres.filter(p => (p.nombre || '').toLowerCase().includes(currentSearch) || (p.apellido || '').toLowerCase().includes(currentSearch) || (p.cedula || '').toLowerCase().includes(currentSearch) || (p.telefono || '').toLowerCase().includes(currentSearch));
                        } else if (tableId === 'representantesTable') {
                            filteredData = allRepresentantes.filter(r => (r.nombre || '').toLowerCase().includes(currentSearch) || (r.apellido || '').toLowerCase().includes(currentSearch) || (r.cedula || '').toLowerCase().includes(currentSearch) || (r.telefono || '').toLowerCase().includes(currentSearch));
                        } else if (tableId === 'inscripcionesTable') {
                             filteredData = allInscripciones.filter(i => 
                                (i.estudiante_nombre || '').toLowerCase().includes(currentSearch) ||
                                (i.nivel_grupo_nombre || '').toLowerCase().includes(currentSearch) ||
                                (i.madre_nombre || '').toLowerCase().includes(currentSearch) ||
                                (i.padre_nombre || '').toLowerCase().includes(currentSearch) ||
                                (i.representante_nombre || '').toLowerCase().includes(currentSearch)
                            );
                        } else if (tableId === 'usersTable') {
                            filteredData = allUsers.filter(u => 
                                (u.username || '').toLowerCase().includes(currentSearch) ||
                                (u.role || '').toLowerCase().includes(currentSearch)
                            );
                        }
                        
                        // Ajustar la función de renderizado según la tabla
                        if (tableId === 'estudiantesTable') {
                            renderEstudiantesTable(filteredData, currentPage);
                        } else if (tableId === 'nivelesGruposTable') {
                            renderNivelesGruposTable(filteredData, currentPage);
                        } else if (tableId === 'madresTable') {
                            renderMadresTable(filteredData, currentPage);
                        } else if (tableId === 'padresTable') {
                            renderPadresTable(filteredData, currentPage);
                        } else if (tableId === 'representantesTable') {
                            renderRepresentantesTable(filteredData, currentPage);
                        } else if (tableId === 'inscripcionesTable') {
                            renderInscripcionesTable(filteredData, currentPage);
                        } else if (tableId === 'usersTable') {
                            renderUsersTable(filteredData, currentPage);
                        }
                        renderPagination(totalRows, paginationContainerId, tableId); // Volver a renderizar para actualizar el estado activo
                    });
                    paginationContainer.appendChild(button);
                }
            }


            estudianteForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(estudianteForm);
                const data = Object.fromEntries(formData.entries());

                // Asegurarse de que los IDs de relaciones sean null si están vacíos
                data.nivel_grupo_id = data.nivel_grupo_id === '' ? null : data.nivel_grupo_id;
                data.madre_id = data.madre_id === '' ? null : data.madre_id;
                data.padre_id = data.padre_id === '' ? null : data.padre_id;
                data.representante_id = data.representante_id === '' ? null : data.representante_id;


                let result;
                if (estudianteIdField.value) {
                    result = await putData('estudiantes', estudianteIdField.value, data);
                } else {
                    result = await postData('estudiantes', data);
                }

                if (result && result.success) {
                    estudianteForm.reset();
                    estudianteIdField.value = '';
                    fetchEstudiantes(); // Refrescar tabla de estudiantes
                    fetchEstudiantesForSelect('estudiante_id_inscripcion'); // Refrescar select en inscripciones
                    showCustomAlert('Operación exitosa!');
                } else {
                    showCustomAlert('Error: ' + (result.message || 'Error desconocido.'));
                }
            });

            estudiantesTableBody.addEventListener('click', async (e) => {
                if (e.target.classList.contains('edit-btn')) {
                    const id = e.target.dataset.id;
                    const estudiante = allEstudiantes.find(e => e.id == id);
                    if (estudiante) {
                        estudianteIdField.value = estudiante.id;
                        document.getElementById('nombre_completo').value = estudiante.nombre_completo;
                        document.getElementById('fecha_nacimiento').value = estudiante.fecha_nacimiento;
                        document.getElementById('lugar_nacimiento').value = estudiante.lugar_nacimiento || '';
                        document.getElementById('entidad_federal').value = estudiante.entidad_federal || '';
                        document.getElementById('nacionalidad_nino').value = estudiante.nacionalidad_nino || '';
                        document.getElementById('condiciones_medicas').value = estudiante.condiciones_medicas || '';
                        
                        await fetchNivelesGruposForSelect('nivel_grupo_id');
                        document.getElementById('nivel_grupo_id').value = estudiante.nivel_grupo_id || '';
                        await fetchMadresForSelect('madre_id');
                        document.getElementById('madre_id').value = estudiante.madre_id || '';
                        await fetchPadresForSelect('padre_id');
                        document.getElementById('padre_id').value = estudiante.padre_id || '';
                        await fetchRepresentantesForSelect('representante_id');
                        document.getElementById('representante_id').value = estudiante.representante_id || '';
                    }
                } else if (e.target.classList.contains('delete-btn')) {
                    const id = e.target.dataset.id;
                    showConfirmModal('¿Estás seguro de que quieres eliminar este estudiante?', async () => {
                        const result = await deleteData('estudiantes', id);
                        if (result && result.success) {
                            fetchEstudiantes();
                            fetchEstudiantesForSelect('estudiante_id_inscripcion'); // Refresh select for inscripciones
                            fetchInscripciones(); // Refresh inscripciones as student_id might become null
                        } else {
                            showCustomAlert('Error al eliminar estudiante: ' + (result.message || 'Error desconocido.'));
                        }
                    });
                }
            });

            cancelarEstudianteBtn.addEventListener('click', () => {
                estudianteForm.reset();
                estudianteIdField.value = '';
            });

            searchInput.addEventListener('keyup', () => {
                const searchTerm = searchInput.value.toLowerCase();
                const filteredEstudiantes = allEstudiantes.filter(estudiante => 
                    (estudiante.nombre_completo || '').toLowerCase().includes(searchTerm) ||
                    (estudiante.lugar_nacimiento || '').toLowerCase().includes(searchTerm) ||
                    (estudiante.nacionalidad_nino || '').toLowerCase().includes(searchTerm) ||
                    (allNivelesGrupos.find(ng => ng.id == estudiante.nivel_grupo_id)?.nombre_nivel_grupo || '').toLowerCase().includes(searchTerm) ||
                    (`${allMadres.find(m => m.id == estudiante.madre_id)?.nombre || ''} ${allMadres.find(m => m.id == estudiante.madre_id)?.apellido || ''}`).toLowerCase().includes(searchTerm) ||
                    (`${allPadres.find(p => p.id == estudiante.padre_id)?.nombre || ''} ${allPadres.find(p => p.id == e.padre_id)?.apellido || ''}`).toLowerCase().includes(searchTerm) ||
                    (`${allRepresentantes.find(r => r.id == estudiante.representante_id)?.nombre || ''} ${allRepresentantes.find(r => r.id == estudiante.representante_id)?.apellido || ''}`).toLowerCase().includes(searchTerm) ||
                    (estudiante.condiciones_medicas || '').toLowerCase().includes(searchTerm)
                );
                currentPage = 1;
                renderEstudiantesTable(filteredEstudiantes, currentPage);
                renderPagination(filteredEstudiantes.length, 'estudiantesPagination', 'estudiantesTable');
            });
            
            exportEstudiantesPdfBtn.addEventListener('click', () => {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('landscape');

                const tableColumn = ["ID", "Nombre Completo", "F. Nacimiento", "Lugar Nac.", "Nacionalidad", "Nivel/Grupo", "Madre", "Padre", "Representante", "Condiciones Médicas"];
                const tableRows = [];

                allEstudiantes.forEach(estudiante => {
                    const nivelGrupo = allNivelesGrupos.find(ng => ng.id == estudiante.nivel_grupo_id);
                    const madre = allMadres.find(m => m.id == estudiante.madre_id);
                    const padre = allPadres.find(p => p.id == estudiante.padre_id);
                    const representante = allRepresentantes.find(r => r.id == estudiante.representante_id);

                    const rowData = [
                        estudiante.id,
                        estudiante.nombre_completo || 'N/A',
                        estudiante.fecha_nacimiento || 'N/A',
                        estudiante.lugar_nacimiento || 'N/A',
                        estudiante.nacionalidad_nino || 'N/A',
                        nivelGrupo ? (nivelGrupo.nombre_nivel_grupo || 'N/A') : 'N/A',
                        madre ? `${madre.nombre || 'N/A'} ${(madre.apellido || '')}`.trim() : 'N/A',
                        padre ? `${padre.nombre || 'N/A'} ${(padre.apellido || '')}`.trim() : 'N/A',
                        representante ? `${representante.nombre || 'N/A'} ${(representante.apellido || '')}`.trim() : 'N/A',
                        estudiante.condiciones_medicas || 'N/A'
                    ];
                    tableRows.push(rowData);
                });

                doc.autoTable(tableColumn, tableRows, { startY: 20 });
                doc.text("Reporte de Estudiantes", 14, 15);
                doc.save("estudiantes_reporte.pdf");
            });


            // --- Lógica para Niveles y Grupos ---
            const nivelGrupoForm = document.getElementById('nivelGrupoForm');
            const nivelesGruposTableBody = document.getElementById('nivelesGruposTable').querySelector('tbody');
            const nivelGrupoIdField = document.getElementById('nivelGrupoId');
            const cancelarNivelGrupoBtn = document.getElementById('cancelarNivelGrupoBtn');
            const searchNivelGrupo = document.getElementById('searchNivelGrupo');
            const exportNivelGrupoPdfBtn = document.getElementById('exportNivelGrupoPdfBtn');

            async function fetchNivelesGrupos() {
                const data = await fetchData('niveles_grupos');
                if (data) {
                    allNivelesGrupos = data;
                    renderNivelesGruposTable(allNivelesGrupos, currentPage);
                    renderPagination(allNivelesGrupos.length, 'nivelesGruposPagination', 'nivelesGruposTable');
                }
            }

            function renderNivelesGruposTable(data, page) {
                nivelesGruposTableBody.innerHTML = '';
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const paginatedData = data.slice(start, end);

                paginatedData.forEach(item => {
                    const row = nivelesGruposTableBody.insertRow();
                    row.className = 'hover:bg-gray-50'; // Añadir clase hover
                    row.innerHTML = `
                        <td>${item.id}</td>
                        <td>${item.nombre_nivel_grupo}</td>
                        <td class="action-buttons">
                            <button class="edit-btn" data-id="${item.id}" data-entity="niveles_grupos">Editar</button>
                            <button class="delete-btn" data-id="${item.id}" data-entity="niveles_grupos">Eliminar</button>
                        </td>
                    `;
                });
            }

            nivelGrupoForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(nivelGrupoForm);
                const data = Object.fromEntries(formData.entries());

                let result;
                if (nivelGrupoIdField.value) {
                    result = await putData('niveles_grupos', nivelGrupoIdField.value, data);
                } else {
                    result = await postData('niveles_grupos', data);
                }

                if (result && result.success) {
                    nivelGrupoForm.reset();
                    nivelGrupoIdField.value = '';
                    fetchNivelesGrupos();
                    fetchNivelesGruposForSelect('nivel_grupo_id');
                    fetchNivelesGruposForSelect('nivel_grupo_id_inscripcion');
                    showCustomAlert('Operación exitosa!');
                } else {
                    showCustomAlert('Error: ' + (result.message || 'Error desconocido.'));
                }
            });

            nivelesGruposTableBody.addEventListener('click', async (e) => {
                if (e.target.classList.contains('edit-btn')) {
                    const id = e.target.dataset.id;
                    const item = allNivelesGrupos.find(i => i.id == id);
                    if (item) {
                        nivelGrupoIdField.value = item.id;
                        document.getElementById('nombre_nivel_grupo').value = item.nombre_nivel_grupo;
                    }
                } else if (e.target.classList.contains('delete-btn')) {
                    const id = e.target.dataset.id;
                    showConfirmModal('¿Estás seguro de que quieres eliminar este nivel/grupo?', async () => {
                        const result = await deleteData('niveles_grupos', id);
                        if (result && result.success) {
                            fetchNivelesGrupos();
                            fetchNivelesGruposForSelect('nivel_grupo_id');
                            fetchNivelesGruposForSelect('nivel_grupo_id_inscripcion');
                            showCustomAlert('Nivel/Grupo eliminado exitosamente!');
                        } else {
                            showCustomAlert('Error al eliminar nivel/grupo: ' + (result.message || 'Error desconocido.'));
                        }
                    });
                }
            });

            cancelarNivelGrupoBtn.addEventListener('click', () => {
                nivelGrupoForm.reset();
                nivelGrupoIdField.value = '';
            });

            searchNivelGrupo.addEventListener('keyup', () => {
                const searchTerm = searchNivelGrupo.value.toLowerCase();
                const filteredNivelesGrupos = allNivelesGrupos.filter(item => 
                    (item.nombre_nivel_grupo || '').toLowerCase().includes(searchTerm)
                );
                currentPage = 1;
                renderNivelesGruposTable(filteredNivelesGrupos, currentPage);
                renderPagination(filteredNivelesGrupos.length, 'nivelesGruposPagination', 'nivelesGruposTable');
            });

            exportNivelGrupoPdfBtn.addEventListener('click', () => {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                const tableColumn = ["ID", "Nombre Nivel/Grupo"];
                const tableRows = [];

                allNivelesGrupos.forEach(item => {
                    tableRows.push([item.id, item.nombre_nivel_grupo]);
                });

                doc.autoTable(tableColumn, tableRows, { startY: 20 });
                doc.text("Reporte de Niveles y Grupos", 14, 15);
                doc.save("niveles_grupos_reporte.pdf");
            });

            // --- Lógica para Madres ---
            const madreForm = document.getElementById('madreForm');
            const madresTableBody = document.getElementById('madresTable').querySelector('tbody');
            const madreIdField = document.getElementById('madreId');
            const cancelarMadreBtn = document.getElementById('cancelarMadreBtn');
            const searchMadre = document.getElementById('searchMadre');
            const exportMadresPdfBtn = document.getElementById('exportMadresPdfBtn');

            async function fetchMadres() {
                const data = await fetchData('madres');
                if (data) {
                    allMadres = data;
                    renderMadresTable(allMadres, currentPage);
                    renderPagination(allMadres.length, 'madresPagination', 'madresTable');
                }
            }

            function renderMadresTable(data, page) {
                madresTableBody.innerHTML = '';
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const paginatedData = data.slice(start, end);

                paginatedData.forEach(item => {
                    const row = madresTableBody.insertRow();
                    row.className = 'hover:bg-gray-50';
                    row.innerHTML = `
                        <td>${item.id}</td>
                        <td>${item.nombre || 'N/A'}</td>
                        <td>${item.apellido || 'N/A'}</td>
                        <td>${item.cedula || ''}</td>
                        <td>${item.telefono || ''}</td>
                        <td class="action-buttons">
                            <button class="edit-btn" data-id="${item.id}" data-entity="madres">Editar</button>
                            <button class="delete-btn" data-id="${item.id}" data-entity="madres">Eliminar</button>
                        </td>
                    `;
                });
            }

            madreForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(madreForm);
                const data = Object.fromEntries(formData.entries());

                let result;
                if (madreIdField.value) {
                    result = await putData('madres', madreIdField.value, data);
                } else {
                    result = await postData('madres', data);
                }

                if (result && result.success) {
                    madreForm.reset();
                    madreIdField.value = '';
                    fetchMadres();
                    fetchMadresForSelect('madre_id');
                    fetchMadresForSelect('madre_id_inscripcion');
                    showCustomAlert('Operación exitosa!');
                } else {
                    showCustomAlert('Error: ' + (result.message || 'Error desconocido.'));
                }
            });

            madresTableBody.addEventListener('click', async (e) => {
                if (e.target.classList.contains('edit-btn')) {
                    const id = e.target.dataset.id;
                    const item = allMadres.find(i => i.id == id);
                    if (item) {
                        madreIdField.value = item.id;
                        document.getElementById('nombre_madre').value = item.nombre;
                        document.getElementById('apellido_madre').value = item.apellido;
                        document.getElementById('cedula_madre').value = item.cedula || '';
                        document.getElementById('telefono_madre').value = item.telefono || '';
                    }
                } else if (e.target.classList.contains('delete-btn')) {
                    const id = e.target.dataset.id;
                    showConfirmModal('¿Estás seguro de que quieres eliminar esta madre?', async () => {
                        const result = await deleteData('madres', id);
                        if (result && result.success) {
                            fetchMadres();
                            fetchMadresForSelect('madre_id');
                            fetchMadresForSelect('madre_id_inscripcion');
                            showCustomAlert('Madre eliminada exitosamente!');
                        } else {
                            showCustomAlert('Error al eliminar madre: ' + (result.message || 'Error desconocido.'));
                        }
                    });
                }
            });

            cancelarMadreBtn.addEventListener('click', () => {
                madreForm.reset();
                madreIdField.value = '';
            });

            searchMadre.addEventListener('keyup', () => {
                const searchTerm = searchMadre.value.toLowerCase();
                const filteredMadres = allMadres.filter(item => 
                    (item.nombre || '').toLowerCase().includes(searchTerm) ||
                    (item.apellido || '').toLowerCase().includes(searchTerm) ||
                    (item.cedula || '').toLowerCase().includes(searchTerm) ||
                    (item.telefono || '').toLowerCase().includes(searchTerm)
                );
                currentPage = 1;
                renderMadresTable(filteredMadres, currentPage);
                renderPagination(filteredMadres.length, 'madresPagination', 'madresTable');
            });

            exportMadresPdfBtn.addEventListener('click', () => {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                const tableColumn = ["ID", "Nombre", "Apellido", "Cédula", "Teléfono"];
                const tableRows = [];

                allMadres.forEach(item => {
                    tableRows.push([
                        item.id, 
                        item.nombre || 'N/A', 
                        item.apellido || 'N/A', 
                        item.cedula || 'N/A', 
                        item.telefono || 'N/A'
                    ]);
                });

                doc.autoTable(tableColumn, tableRows, { startY: 20 });
                doc.text("Reporte de Madres", 14, 15);
                doc.save("madres_reporte.pdf");
            });

            // --- Lógica para Padres ---
            const padreForm = document.getElementById('padreForm');
            const padresTableBody = document.getElementById('padresTable').querySelector('tbody');
            const padreIdField = document.getElementById('padreId');
            const cancelarPadreBtn = document.getElementById('cancelarPadreBtn');
            const searchPadre = document.getElementById('searchPadre');
            const exportPadresPdfBtn = document.getElementById('exportPadresPdfBtn');

            async function fetchPadres() {
                const data = await fetchData('padres');
                if (data) {
                    allPadres = data;
                    renderPadresTable(allPadres, currentPage);
                    renderPagination(allPadres.length, 'padresPagination', 'padresTable');
                }
            }

            function renderPadresTable(data, page) {
                padresTableBody.innerHTML = '';
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const paginatedData = data.slice(start, end);

                paginatedData.forEach(item => {
                    const row = padresTableBody.insertRow();
                    row.className = 'hover:bg-gray-50';
                    row.innerHTML = `
                        <td>${item.id}</td>
                        <td>${item.nombre || 'N/A'}</td>
                        <td>${item.apellido || 'N/A'}</td>
                        <td>${item.cedula || ''}</td>
                        <td>${item.telefono || ''}</td>
                        <td class="action-buttons">
                            <button class="edit-btn" data-id="${item.id}" data-entity="padres">Editar</button>
                            <button class="delete-btn" data-id="${item.id}" data-entity="padres">Eliminar</button>
                        </td>
                    `;
                });
            }

            padreForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(padreForm);
                const data = Object.fromEntries(formData.entries());

                let result;
                if (padreIdField.value) {
                    result = await putData('padres', padreIdField.value, data);
                } else {
                    result = await postData('padres', data);
                }

                if (result && result.success) {
                    padreForm.reset();
                    padreIdField.value = '';
                    fetchPadres();
                    fetchPadresForSelect('padre_id');
                    fetchPadresForSelect('padre_id_inscripcion');
                    showCustomAlert('Operación exitosa!');
                } else {
                    showCustomAlert('Error: ' + (result.message || 'Error desconocido.'));
                }
            });

            padresTableBody.addEventListener('click', async (e) => {
                if (e.target.classList.contains('edit-btn')) {
                    const id = e.target.dataset.id;
                    const item = allPadres.find(i => i.id == id);
                    if (item) {
                        padreIdField.value = item.id;
                        document.getElementById('nombre_padre').value = item.nombre;
                        document.getElementById('apellido_padre').value = item.apellido;
                        document.getElementById('cedula_padre').value = item.cedula || '';
                        document.getElementById('telefono_padre').value = item.telefono || '';
                    }
                } else if (e.target.classList.contains('delete-btn')) {
                    const id = e.target.dataset.id;
                    showConfirmModal('¿Estás seguro de que quieres eliminar este padre?', async () => {
                        const result = await deleteData('padres', id);
                        if (result && result.success) {
                            fetchPadres();
                            fetchPadresForSelect('padre_id');
                            fetchPadresForSelect('padre_id_inscripcion');
                            showCustomAlert('Padre eliminado exitosamente!');
                        } else {
                            showCustomAlert('Error al eliminar padre: ' + (result.message || 'Error desconocido.'));
                        }
                    });
                }
            });

            cancelarPadreBtn.addEventListener('click', () => {
                padreForm.reset();
                padreIdField.value = '';
            });

            searchPadre.addEventListener('keyup', () => {
                const searchTerm = searchPadre.value.toLowerCase();
                const filteredPadres = allPadres.filter(item => 
                    (item.nombre || '').toLowerCase().includes(searchTerm) ||
                    (item.apellido || '').toLowerCase().includes(searchTerm) ||
                    (item.cedula || '').toLowerCase().includes(searchTerm) ||
                    (item.telefono || '').toLowerCase().includes(searchTerm)
                );
                currentPage = 1;
                renderPadresTable(filteredPadres, currentPage);
                renderPagination(filteredPadres.length, 'padresPagination', 'padresTable');
            });

            exportPadresPdfBtn.addEventListener('click', () => {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                const tableColumn = ["ID", "Nombre", "Apellido", "Cédula", "Teléfono"];
                const tableRows = [];

                allPadres.forEach(item => {
                    tableRows.push([
                        item.id, 
                        item.nombre || 'N/A', 
                        item.apellido || 'N/A', 
                        item.cedula || 'N/A', 
                        item.telefono || 'N/A'
                    ]);
                });

                doc.autoTable(tableColumn, tableRows, { startY: 20 });
                doc.text("Reporte de Padres", 14, 15);
                doc.save("padres_reporte.pdf");
            });


            // --- Lógica para Representantes ---
            const representanteForm = document.getElementById('representanteForm');
            const representantesTableBody = document.getElementById('representantesTable').querySelector('tbody');
            const representanteIdField = document.getElementById('representanteId');
            const cancelarRepresentanteBtn = document.getElementById('cancelarRepresentanteBtn');
            const searchRepresentante = document.getElementById('searchRepresentante');
            const exportRepresentantesPdfBtn = document.getElementById('exportRepresentantesPdfBtn');

            async function fetchRepresentantes() {
                const data = await fetchData('representantes');
                if (data) {
                    allRepresentantes = data;
                    renderRepresentantesTable(allRepresentantes, currentPage);
                    renderPagination(allRepresentantes.length, 'representantesPagination', 'representantesTable');
                }
            }

            function renderRepresentantesTable(data, page) {
                representantesTableBody.innerHTML = '';
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const paginatedData = data.slice(start, end);

                paginatedData.forEach(item => {
                    const row = representantesTableBody.insertRow();
                    row.className = 'hover:bg-gray-50';
                    row.innerHTML = `
                        <td>${item.id}</td>
                        <td>${item.nombre || 'N/A'}</td>
                        <td>${item.apellido || 'N/A'}</td>
                        <td>${item.cedula || ''}</td>
                        <td>${item.telefono || ''}</td>
                        <td class="action-buttons">
                            <button class="edit-btn" data-id="${item.id}" data-entity="representantes">Editar</button>
                            <button class="delete-btn" data-id="${item.id}" data-entity="representantes">Eliminar</button>
                        </td>
                    `;
                });
            }

            representanteForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(representanteForm);
                const data = Object.fromEntries(formData.entries());

                let result;
                if (representanteIdField.value) {
                    result = await putData('representantes', representanteIdField.value, data);
                } else {
                    result = await postData('representantes', data);
                }

                if (result && result.success) {
                    representanteForm.reset();
                    representanteIdField.value = '';
                    fetchRepresentantes();
                    fetchRepresentantesForSelect('representante_id');
                    fetchRepresentantesForSelect('representante_id_inscripcion');
                    showCustomAlert('Operación exitosa!');
                } else {
                    showCustomAlert('Error: ' + (result.message || 'Error desconocido.'));
                }
            });

            representantesTableBody.addEventListener('click', async (e) => {
                if (e.target.classList.contains('edit-btn')) {
                    const id = e.target.dataset.id;
                    const item = allRepresentantes.find(i => i.id == id);
                    if (item) {
                        representanteIdField.value = item.id;
                        document.getElementById('nombre_representante').value = item.nombre;
                        document.getElementById('apellido_representante').value = item.apellido;
                        document.getElementById('cedula_representante').value = item.cedula || '';
                        document.getElementById('telefono_representante').value = item.telefono || '';
                    }
                } else if (e.target.classList.contains('delete-btn')) {
                    const id = e.target.dataset.id;
                    showConfirmModal('¿Estás seguro de que quieres eliminar este representante?', async () => {
                        const result = await deleteData('representantes', id);
                        if (result && result.success) {
                            fetchRepresentantes();
                            fetchRepresentantesForSelect('representante_id');
                            fetchRepresentantesForSelect('representante_id_inscripcion');
                            showCustomAlert('Representante eliminado exitosamente!');
                        } else {
                            showCustomAlert('Error al eliminar representante: ' + (result.message || 'Error desconocido.'));
                        }
                    });
                }
            });

            cancelarRepresentanteBtn.addEventListener('click', () => {
                representanteForm.reset();
                representanteIdField.value = '';
            });

            searchRepresentante.addEventListener('keyup', () => {
                const searchTerm = searchRepresentante.value.toLowerCase();
                const filteredRepresentantes = allRepresentantes.filter(item => 
                    (item.nombre || '').toLowerCase().includes(searchTerm) ||
                    (item.apellido || '').toLowerCase().includes(searchTerm) ||
                    (item.cedula || '').toLowerCase().includes(searchTerm) ||
                    (item.telefono || '').toLowerCase().includes(searchTerm)
                );
                currentPage = 1;
                renderRepresentantesTable(filteredRepresentantes, currentPage);
                renderPagination(filteredRepresentantes.length, 'representantesPagination', 'representantesTable');
            });

            exportRepresentantesPdfBtn.addEventListener('click', () => {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                const tableColumn = ["ID", "Nombre", "Apellido", "Cédula", "Teléfono"];
                const tableRows = [];

                allRepresentantes.forEach(item => {
                    tableRows.push([
                        item.id, 
                        item.nombre || 'N/A', 
                        item.apellido || 'N/A', 
                        item.cedula || 'N/A', 
                        item.telefono || 'N/A'
                    ]);
                });

                doc.autoTable(tableColumn, tableRows, { startY: 20 });
                doc.text("Reporte de Representantes", 14, 15);
                doc.save("representantes_reporte.pdf");
            });


            // --- Lógica para Inscripciones ---
            const inscripcionForm = document.getElementById('inscripcionForm');
            const inscripcionesTableBody = document.getElementById('inscripcionesTable').querySelector('tbody');
            const inscripcionIdField = document.getElementById('inscripcionId');
            const cancelarInscripcionBtn = document.getElementById('cancelarInscripcionBtn');
            const searchInscripcion = document.getElementById('searchInscripcion');
            const exportInscripcionesPdfBtn = document.getElementById('exportInscripcionesPdfBtn');

            async function fetchInscripciones() {
                await fetchEstudiantesForSelect('estudiante_id_inscripcion');
                await fetchNivelesGruposForSelect('nivel_grupo_id_inscripcion');
                await fetchMadresForSelect('madre_id_inscripcion');
                await fetchPadresForSelect('padre_id_inscripcion');
                await fetchRepresentantesForSelect('representante_id_inscripcion');
                
                const data = await fetchData('inscripciones'); 
                if (data) {
                    allInscripciones = data.map(insc => ({
                        ...insc,
                        estudiante_nombre: allEstudiantes.find(e => e.id == insc.estudiante_id)?.nombre_completo || 'N/A',
                        nivel_grupo_nombre: allNivelesGrupos.find(ng => ng.id == insc.nivel_grupo_id)?.nombre_nivel_grupo || 'N/A',
                        madre_nombre: insc.madre_id ? `${allMadres.find(m => m.id == insc.madre_id)?.nombre || ''} ${(allMadres.find(m => m.id == insc.madre_id)?.apellido || '')}`.trim() : 'N/A',
                        padre_nombre: insc.padre_id ? `${allPadres.find(p => p.id == insc.padre_id)?.nombre || ''} ${(allPadres.find(p => p.id == insc.padre_id)?.apellido || '')}`.trim() : 'N/A',
                        representante_nombre: insc.representante_id ? `${allRepresentantes.find(r => r.id == insc.representante_id)?.nombre || ''} ${(allRepresentantes.find(r => r.id == insc.representante_id)?.apellido || '')}`.trim() : 'N/A'
                    }));
                    renderInscripcionesTable(allInscripciones, currentPage);
                    renderPagination(allInscripciones.length, 'inscripcionesPagination', 'inscripcionesTable');
                }
            }
            
            function renderInscripcionesTable(data, page) {
                inscripcionesTableBody.innerHTML = '';
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const paginatedData = data.slice(start, end);

                paginatedData.forEach(item => {
                    const row = inscripcionesTableBody.insertRow();
                    row.className = 'hover:bg-gray-50';
                    row.innerHTML = `
                        <td>${item.id}</td>
                        <td>${item.estudiante_nombre}</td>
                        <td>${item.nivel_grupo_nombre}</td>
                        <td>${item.madre_nombre}</td>
                        <td>${item.padre_nombre}</td>
                        <td>${item.representante_nombre}</td>
                        <td class="action-buttons">
                            <button class="edit-btn" data-id="${item.id}" data-entity="inscripciones">Editar</button>
                            <button class="delete-btn" data-id="${item.id}" data-entity="inscripciones">Eliminar</button>
                        </td>
                    `;
                });
            }

            inscripcionForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(inscripcionForm);
                const data = Object.fromEntries(formData.entries());

                data.madre_id = data.madre_id === '' ? null : data.madre_id;
                data.padre_id = data.padre_id === '' ? null : data.padre_id;

                let result;
                if (inscripcionIdField.value) {
                    result = await putData('inscripciones', inscripcionIdField.value, data);
                } else {
                    result = await postData('inscripciones', data);
                }

                if (result && result.success) {
                    inscripcionForm.reset();
                    inscripcionIdField.value = '';
                    fetchInscripciones();
                    showCustomAlert('Operación exitosa!');
                } else {
                    showCustomAlert('Error: ' + (result.message || 'Error desconocido.'));
                }
            });

            inscripcionesTableBody.addEventListener('click', async (e) => {
                if (e.target.classList.contains('edit-btn')) {
                    const id = e.target.dataset.id;
                    const item = allInscripciones.find(i => i.id == id);
                    if (item) {
                        inscripcionIdField.value = item.id;
                        document.getElementById('estudiante_id_inscripcion').value = item.estudiante_id || '';
                        document.getElementById('nivel_grupo_id_inscripcion').value = item.nivel_grupo_id || '';
                        document.getElementById('madre_id_inscripcion').value = item.madre_id || '';
                        document.getElementById('padre_id_inscripcion').value = item.padre_id || '';
                        document.getElementById('representante_id_inscripcion').value = item.representante_id || '';
                    }
                } else if (e.target.classList.contains('delete-btn')) {
                    const id = e.target.dataset.id;
                    showConfirmModal('¿Estás seguro de que quieres eliminar esta inscripción?', async () => {
                        const result = await deleteData('inscripciones', id);
                        if (result && result.success) {
                            fetchInscripciones();
                            showCustomAlert('Inscripción eliminada exitosamente!');
                        } else {
                            showCustomAlert('Error al eliminar inscripción: ' + (result.message || 'Error desconocido.'));
                        }
                    });
                }
            });

            cancelarInscripcionBtn.addEventListener('click', () => {
                inscripcionForm.reset();
                inscripcionIdField.value = '';
            });

            searchInscripcion.addEventListener('keyup', () => {
                const searchTerm = searchInscripcion.value.toLowerCase();
                const filteredInscripciones = allInscripciones.filter(item => 
                    (item.estudiante_nombre || '').toLowerCase().includes(searchTerm) ||
                    (item.nivel_grupo_nombre || '').toLowerCase().includes(searchTerm) ||
                    (item.madre_nombre || '').toLowerCase().includes(searchTerm) ||
                    (item.padre_nombre || '').toLowerCase().includes(searchTerm) ||
                    (item.representante_nombre || '').toLowerCase().includes(searchTerm)
                );
                currentPage = 1;
                renderInscripcionesTable(filteredInscripciones, currentPage);
                renderPagination(filteredInscripciones.length, 'inscripcionesPagination', 'inscripcionesTable');
            });

            exportInscripcionesPdfBtn.addEventListener('click', () => {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('landscape');

                const tableColumn = ["ID", "Estudiante", "Nivel/Grupo", "Madre", "Padre", "Representante"];
                const tableRows = [];

                allInscripciones.forEach(item => {
                    tableRows.push([
                        item.id, 
                        item.estudiante_nombre, 
                        item.nivel_grupo_nombre, 
                        item.madre_nombre, 
                        item.padre_nombre, 
                        item.representante_nombre
                    ]);
                });

                doc.autoTable(tableColumn, tableRows, { startY: 20 });
                doc.text("Reporte de Inscripciones", 14, 15);
                doc.save("inscripciones_reporte.pdf");
            });


            // --- Lógica para rellenar selects de Inscripciones y Estudiantes ---
            async function fetchEstudiantesForSelect(selectId) {
                const select = document.getElementById(selectId);
                const data = await fetchData('estudiantes');
                if (data) {
                    allEstudiantes = data;
                    select.innerHTML = '<option value="">Selecciona un estudiante</option>';
                    data.forEach(est => {
                        const option = document.createElement('option');
                        option.value = est.id;
                        option.textContent = `${est.nombre_completo}`;
                        select.appendChild(option);
                    });
                }
            }

            async function fetchNivelesGruposForSelect(selectId) {
                const select = document.getElementById(selectId);
                const data = await fetchData('niveles_grupos');
                if (data) {
                    allNivelesGrupos = data;
                    select.innerHTML = '<option value="">Selecciona un nivel/grupo</option>';
                    data.forEach(ng => {
                        const option = document.createElement('option');
                        option.value = ng.id;
                        option.textContent = ng.nombre_nivel_grupo;
                        select.appendChild(option);
                    });
                }
            }
            
            async function fetchMadresForSelect(selectId) {
                const select = document.getElementById(selectId);
                const data = await fetchData('madres');
                if (data) {
                    allMadres = data;
                    select.innerHTML = '<option value="">Ninguna</option>';
                    data.forEach(madre => {
                        const option = document.createElement('option');
                        option.value = madre.id;
                        option.textContent = `${madre.nombre} ${madre.apellido || ''}`.trim();
                        select.appendChild(option);
                    });
                }
            }

            async function fetchPadresForSelect(selectId) {
                const select = document.getElementById(selectId);
                const data = await fetchData('padres');
                if (data) {
                    allPadres = data;
                    select.innerHTML = '<option value="">Ninguno</option>';
                    data.forEach(padre => {
                        const option = document.createElement('option');
                        option.value = padre.id;
                        option.textContent = `${padre.nombre} ${padre.apellido || ''}`.trim();
                        select.appendChild(option);
                    });
                }
            }

            async function fetchRepresentantesForSelect(selectId) {
                const select = document.getElementById(selectId);
                const data = await fetchData('representantes');
                if (data) {
                    allRepresentantes = data;
                    select.innerHTML = '<option value="">Selecciona un representante</option>';
                    data.forEach(rep => {
                        const option = document.createElement('option');
                        option.value = rep.id;
                        option.textContent = `${rep.nombre} ${rep.apellido || ''}`.trim();
                        select.appendChild(option);
                    });
                }
            }

            // --- Lógica para Copia de Seguridad ---
            const generarBackupBtn = document.getElementById('generarBackupBtn');
            const backupStatusDiv = document.getElementById('backupStatus');

            generarBackupBtn.addEventListener('click', async () => {
                backupStatusDiv.classList.remove('hidden');
                backupStatusDiv.classList.add('bg-blue-100', 'text-blue-800');
                backupStatusDiv.textContent = 'Generando copia de seguridad... Esto puede tardar un momento.';

                try {
                    const response = await fetch(`${apiBaseUrl}?entity=backup`);
                    const result = await response.json();

                    if (result.success) {
                        backupStatusDiv.classList.remove('bg-blue-100', 'text-blue-800', 'bg-red-100', 'text-red-800');
                        backupStatusDiv.classList.add('bg-green-100', 'text-green-800');
                        backupStatusDiv.innerHTML = `Copia de seguridad creada exitosamente. <a href="${result.download_url}" class="text-blue-600 hover:underline font-semibold" download>Haz clic aquí para descargarla</a>.`;
                    } else {
                        backupStatusDiv.classList.remove('bg-blue-100', 'text-blue-800', 'bg-green-100', 'text-green-800');
                        backupStatusDiv.classList.add('bg-red-100', 'text-red-800');
                        backupStatusDiv.textContent = 'Error al generar la copia de seguridad: ' + (result.message || 'Error desconocido.');
                    }
                } catch (error) {
                    backupStatusDiv.classList.remove('bg-blue-100', 'text-blue-800', 'bg-green-100', 'text-green-800');
                    backupStatusDiv.classList.add('bg-red-100', 'text-red-800');
                    backupStatusDiv.textContent = 'Error de conexión al servidor al intentar la copia de seguridad: ' + error.message;
                }
            });

            // --- Lógica para Estadísticas ---
            async function fetchEstadisticas() {
                await fetchInscripciones(); 

                if (allEstudiantes && allNivelesGrupos) {
                    renderNivelGrupoChart(allEstudiantes, allNivelesGrupos);
                }
                if (allInscripciones && allNivelesGrupos) {
                    renderInscripcionesChart(allInscripciones, allNivelesGrupos);
                }
            }

            async function renderNivelGrupoChart(estudiantes, nivelesGruposData) {
                const nivelGrupoCounts = {};
                const nivelGrupoNames = {};

                nivelesGruposData.forEach(ng => {
                    nivelGrupoCounts[ng.id] = 0;
                    nivelGrupoNames[ng.id] = ng.nombre_nivel_grupo;
                });

                estudiantes.forEach(est => {
                    if (est.nivel_grupo_id && nivelGrupoCounts.hasOwnProperty(est.nivel_grupo_id)) {
                        nivelGrupoCounts[est.nivel_grupo_id]++;
                    }
                });

                const labels = Object.keys(nivelGrupoCounts).map(id => nivelGrupoNames[id]);
                const data = Object.values(nivelGrupoCounts);

                const ctx = document.getElementById('nivelGrupoChart').getContext('2d');
                if (window.nivelGrupoChartInstance) {
                    window.nivelGrupoChartInstance.destroy();
                }
                window.nivelGrupoChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Número de Estudiantes',
                            data: data,
                            backgroundColor: '#60a5fa',
                            borderColor: '#3b82f6',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false,
                            },
                            title: {
                                display: false,
                                text: 'Estudiantes por Nivel/Grupo'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }

            function renderInscripcionesChart(inscripcionesData, nivelesGruposData) {
                const nivelGrupoEnrollmentCounts = {};
                const nivelGrupoNames = {};

                nivelesGruposData.forEach(ng => {
                    nivelGrupoEnrollmentCounts[ng.id] = 0;
                    nivelGrupoNames[ng.id] = ng.nombre_nivel_grupo;
                });

                inscripcionesData.forEach(insc => {
                    if (insc.nivel_grupo_id && nivelGrupoEnrollmentCounts.hasOwnProperty(insc.nivel_grupo_id)) {
                        nivelGrupoEnrollmentCounts[insc.nivel_grupo_id]++;
                    }
                });

                const labels = Object.keys(nivelGrupoEnrollmentCounts).map(id => nivelGrupoNames[id]);
                const data = Object.values(nivelGrupoEnrollmentCounts);

                const ctx = document.getElementById('inscripcionesChart').getContext('2d');
                if (window.inscripcionesChartInstance) {
                    window.inscripcionesChartInstance.destroy();
                }
                window.inscripcionesChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Número de Inscripciones',
                            data: data,
                            backgroundColor: '#818cf8',
                            borderColor: '#6366f1',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false,
                            },
                            title: {
                                display: false,
                                text: 'Inscripciones por Nivel/Grupo'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }


            // --- Lógica para Cambiar de Pestaña ---
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            function activateTab(tabName) {
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.add('hidden'));

                const targetButton = document.querySelector(`.tab-button[data-tab="${tabName}"]`);
                if (targetButton) {
                    targetButton.classList.add('active');
                    document.getElementById(`tabContent_${tabName}`).classList.remove('hidden');
                    localStorage.setItem('activeTab', tabName); // Guardar la pestaña activa
                    currentActiveTab = tabName; // Actualizar la variable global
                    // Recargar datos específicos de la pestaña si es necesario
                    switch(tabName) {
                        case 'estudiantes':
                            fetchEstudiantes();
                            fetchNivelesGruposForSelect('nivel_grupo_id');
                            fetchMadresForSelect('madre_id');
                            fetchPadresForSelect('padre_id');
                            fetchRepresentantesForSelect('representante_id');
                            break;
                        case 'niveles_grupos':
                            fetchNivelesGrupos();
                            break;
                        case 'madres':
                            fetchMadres();
                            break;
                        case 'padres':
                            fetchPadres();
                            break;
                        case 'representantes':
                            fetchRepresentantes();
                            break;
                        case 'inscripciones':
                            fetchEstudiantesForSelect('estudiante_id_inscripcion');
                            fetchNivelesGruposForSelect('nivel_grupo_id_inscripcion');
                            fetchMadresForSelect('madre_id_inscripcion');
                            fetchPadresForSelect('padre_id_inscripcion');
                            fetchRepresentantesForSelect('representante_id_inscripcion');
                            setTimeout(fetchInscripciones, 100);
                            break;
                        case 'estadisticas':
                            fetchEstadisticas();
                            break;
                        case 'gestion_usuarios':
                            fetchUsers(); // Cargar usuarios al entrar en la pestaña de gestión de usuarios
                            break;
                    }
                }
            }

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    activateTab(button.dataset.tab);
                });
            });

            // Función genérica para el modal de confirmación
            function showConfirmModal(message, onConfirm) {
                const confirmModal = document.createElement('div');
                confirmModal.classList.add('modal');
                confirmModal.innerHTML = `
                    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm text-center">
                        <p class="text-xl font-semibold mb-6">${message}</p>
                        <div class="flex justify-center space-x-4">
                            <button id="confirmYes" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition duration-300 text-lg">Sí</button>
                            <button id="confirmNo" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded-lg shadow-md transition duration-300 text-lg">No</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(confirmModal);

                document.getElementById('confirmYes').addEventListener('click', () => {
                    document.body.removeChild(confirmModal);
                    onConfirm();
                });

                document.getElementById('confirmNo').addEventListener('click', () => {
                    document.body.removeChild(confirmModal);
                });
            }

            // Función genérica para el modal de alerta (sustituto de alert())
            function showCustomAlert(message) {
                const alertModal = document.createElement('div');
                alertModal.classList.add('modal');
                alertModal.innerHTML = `
                    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm text-center">
                        <p class="text-xl font-semibold mb-6">${message}</p>
                        <button id="alertCloseBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition duration-300 text-lg">Aceptar</button>
                    </div>
                `;
                document.body.appendChild(alertModal);
                document.getElementById('alertCloseBtn').addEventListener('click', () => {
                    document.body.removeChild(alertModal);
                });
            }

            // --- Lógica para Gestión de Usuarios (Nueva) ---
            async function fetchUsers() {
                const data = await fetchData('users');
                if (data) {
                    allUsers = data;
                    renderUsersTable(allUsers, currentPage);
                    renderPagination(allUsers.length, 'usersPagination', 'usersTable');
                }
            }

            function renderUsersTable(data, page) {
                usersTableBody.innerHTML = '';
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const paginatedData = data.slice(start, end);

                paginatedData.forEach(user => {
                    const row = usersTableBody.insertRow();
                    row.className = 'hover:bg-gray-50';
                    row.innerHTML = `
                        <td>${user.id}</td>
                        <td>${user.username}</td>
                        <td>${user.role}</td>
                        <td class="action-buttons">
                            <button class="edit-user-btn" data-id="${user.id}">Editar</button>
                            <button class="delete-user-btn" data-id="${user.id}">Eliminar</button>
                        </td>
                    `;
                });
            }

            // Manejar click en botones de editar/eliminar usuario
            usersTableBody.addEventListener('click', async (e) => {
                if (e.target.classList.contains('edit-user-btn')) {
                    const id = e.target.dataset.id;
                    const user = allUsers.find(u => u.id == id);
                    if (user) {
                        editUserIdInput.value = user.id;
                        editUsernameInput.value = user.username;
                        editUserRoleSelect.value = user.role;
                        editUserPasswordInput.value = ''; // Limpiar campo de contraseña al editar
                        editUserError.classList.add('hidden');
                        editUserModal.classList.remove('hidden');
                    }
                } else if (e.target.classList.contains('delete-user-btn')) {
                    const id = e.target.dataset.id;
                    showConfirmModal('¿Estás seguro de que quieres eliminar este usuario?', async () => {
                        const result = await deleteData('users', id);
                        if (result && result.success) {
                            fetchUsers(); // Recargar la tabla de usuarios
                            showCustomAlert('Usuario eliminado exitosamente!');
                        } else {
                            showCustomAlert('Error al eliminar usuario: ' + (result.message || 'Error desconocido.'));
                        }
                    });
                }
            });

            // Manejar envío del formulario de edición de usuario
            editUserForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id = editUserIdInput.value;
                const username = editUsernameInput.value;
                const newPassword = editUserPasswordInput.value;
                const role = editUserRoleSelect.value;

                editUserError.classList.add('hidden');

                const data = { username, role };
                if (newPassword) { // Solo añadir la contraseña si no está vacía
                    if (newPassword.length < 6) {
                        editUserError.textContent = 'La nueva contraseña debe tener al menos 6 caracteres.';
                        editUserError.classList.remove('hidden');
                        return;
                    }
                    data.password = newPassword; // La API hasheará esto
                }

                const result = await putData('users', id, data);

                if (result && result.success) {
                    editUserModal.classList.add('hidden');
                    fetchUsers(); // Recargar la tabla de usuarios
                    showCustomAlert('Usuario actualizado exitosamente!');
                } else {
                    editUserError.textContent = result.message || 'Error al actualizar usuario.';
                    editUserError.classList.remove('hidden');
                }
            });

            // Cancelar edición de usuario
            cancelEditUserBtn.addEventListener('click', () => {
                editUserModal.classList.add('hidden');
                editUserForm.reset();
            });

            // Búsqueda de usuarios
            searchUsersInput.addEventListener('keyup', () => {
                const searchTerm = searchUsersInput.value.toLowerCase();
                const filteredUsers = allUsers.filter(user => 
                    (user.username || '').toLowerCase().includes(searchTerm) ||
                    (user.role || '').toLowerCase().includes(searchTerm)
                );
                currentPage = 1;
                renderUsersTable(filteredUsers, currentPage);
                renderPagination(filteredUsers.length, 'usersPagination', 'usersTable');
            });

            // Exportar usuarios a PDF
            exportUsersPdfBtn.addEventListener('click', () => {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                const tableColumn = ["ID", "Usuario", "Rol"];
                const tableRows = [];

                allUsers.forEach(user => {
                    tableRows.push([user.id, user.username, user.role]);
                });

                doc.autoTable(tableColumn, tableRows, { startY: 20 });
                doc.text("Reporte de Usuarios", 14, 15);
                doc.save("usuarios_reporte.pdf");
            });

            // Función para verificar el estado de login al cargar la página
            function checkLoginStatus() {
                const userRole = localStorage.getItem('userRole');
                if (userRole) {
                    showMainApp(userRole);
                } else {
                    showLoginScreen();
                }
            }

            // Inicialización de la aplicación: verificar si hay un rol guardado
            checkLoginStatus();

        });
    </script>
</body>
</html>

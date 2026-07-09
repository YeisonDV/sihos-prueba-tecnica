<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestión de Pacientes
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div id="mensaje" class="hidden mb-4 p-4 rounded"></div>

                <form id="formPaciente" class="grid grid-cols-2 gap-4 mb-8">
                    <input type="hidden" id="paciente_id">

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
                        <select id="tipo_documento_id" class="w-full border-gray-300 rounded-md" required>
                            <option value="">Seleccione...</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Número de Documento</label>
                        <input type="text" id="numero_documento" class="w-full border-gray-300 rounded-md" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Primer Nombre</label>
                        <input type="text" id="nombre1" class="w-full border-gray-300 rounded-md" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Segundo Nombre</label>
                        <input type="text" id="nombre2" class="w-full border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Primer Apellido</label>
                        <input type="text" id="apellido1" class="w-full border-gray-300 rounded-md" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Segundo Apellido</label>
                        <input type="text" id="apellido2" class="w-full border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Género</label>
                        <select id="genero_id" class="w-full border-gray-300 rounded-md" required>
                            <option value="">Seleccione...</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Correo</label>
                        <input type="email" id="correo" class="w-full border-gray-300 rounded-md" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Departamento</label>
                        <select id="departamento_id" class="w-full border-gray-300 rounded-md" required>
                            <option value="">Seleccione...</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Municipio</label>
                        <select id="municipio_id" class="w-full border-gray-300 rounded-md" required>
                            <option value="">Seleccione...</option>
                        </select>
                    </div>

                    <div class="col-span-2 flex gap-2">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Guardar</button>
                        <button type="button" id="btnCancelar" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hidden">Cancelar edición</button>
                    </div>
                </form>
                <div class="mb-4 flex gap-2">
                    <input type="text" id="buscar" placeholder="Buscar por nombre o correo..." class="border-gray-300 rounded-md w-1/3">
                </div>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="p-2">Documento</th>
                            <th class="p-2">Nombre completo</th>
                            <th class="p-2">Correo</th>
                            <th class="p-2">Género</th>
                            <th class="p-2">Departamento</th>
                            <th class="p-2">Municipio</th>
                            <th class="p-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaPacientes">
                         <!-- Las filas se llenan dinámicamente con JS -->
                    </tbody>
                </table>

                <div id="paginacion" class="mt-4 flex gap-2"></div>
            </div>
        </div>
    </div>
    <script>
    const API = '/pacientes';
    let paginaActual = 1;
    let terminoBusqueda = '';

    // Cargar catálogos al iniciar
    async function cargarCatalogos() {
        const [tipos, generos, departamentos] = await Promise.all([
            fetch('/tipos-documento').then(r => r.json()),
            fetch('/generos').then(r => r.json()),
            fetch('/departamentos').then(r => r.json()),
        ]);

        llenarSelect('tipo_documento_id', tipos);
        llenarSelect('genero_id', generos);
        llenarSelect('departamento_id', departamentos);
    }

    function llenarSelect(idSelect, datos) {
        const select = document.getElementById(idSelect);
        datos.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.nombre;
            select.appendChild(option);
        });
    }

    cargarCatalogos();

    // Cuando cambia el departamento, cargar sus municipios
    document.getElementById('departamento_id').addEventListener('change', async (e) => {
        const departamentoId = e.target.value;
        const selectMunicipio = document.getElementById('municipio_id');

        selectMunicipio.innerHTML = '<option value="">Seleccione...</option>';

        if (!departamentoId) return;

        const municipios = await fetch(`/municipios/${departamentoId}`).then(r => r.json());
        llenarSelect('municipio_id', municipios);
    });

    // Cargar y mostrar la lista de pacientes (con búsqueda y paginación)
    async function cargarPacientes() {
        const params = new URLSearchParams({ page: paginaActual, buscar: terminoBusqueda });
        const respuesta = await fetch(`${API}?${params}`).then(r => r.json());
        renderTabla(respuesta.data);
        renderPaginacion(respuesta);
    }

    function renderTabla(pacientes) {
        const tbody = document.getElementById('tablaPacientes');
        tbody.innerHTML = '';

        if (pacientes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="p-2 text-center text-gray-500">No hay pacientes registrados</td></tr>';
            return;
        }

        pacientes.forEach(p => {
            const nombreCompleto = `${p.nombre1} ${p.nombre2 ?? ''} ${p.apellido1} ${p.apellido2 ?? ''}`.replace(/\s+/g, ' ').trim();

            const fila = document.createElement('tr');
            fila.className = 'border-b';
            fila.innerHTML = `
                <td class="p-2">${p.numero_documento}</td>
                <td class="p-2">${nombreCompleto}</td>
                <td class="p-2">${p.correo}</td>
                <td class="p-2">${p.genero?.nombre ?? ''}</td>
                <td class="p-2">${p.departamento?.nombre ?? ''}</td>
                <td class="p-2">${p.municipio?.nombre ?? ''}</td>
                <td class="p-2">
                    <button onclick="editarPaciente(${p.id})" class="text-indigo-600 mr-2">Editar</button>
                    <button onclick="eliminarPaciente(${p.id})" class="text-red-600">Eliminar</button>
                </td>
            `;
            tbody.appendChild(fila);
        });
    }

    function renderPaginacion(respuesta) {
        const contenedor = document.getElementById('paginacion');
        contenedor.innerHTML = '';

        if (respuesta.last_page <= 1) return;

        for (let i = 1; i <= respuesta.last_page; i++) {
            const boton = document.createElement('button');
            boton.textContent = i;
            boton.className = i === respuesta.current_page
                ? 'bg-indigo-600 text-white px-3 py-1 rounded'
                : 'bg-gray-200 text-gray-800 px-3 py-1 rounded';
            boton.onclick = () => {
                paginaActual = i;
                cargarPacientes();
            };
            contenedor.appendChild(boton);
        }
    }

    document.getElementById('buscar').addEventListener('input', (e) => {
        terminoBusqueda = e.target.value;
        paginaActual = 1;
        cargarPacientes();
    });

    cargarPacientes();

    // Manejar el envío del formulario (crear o actualizar)
    document.getElementById('formPaciente').addEventListener('submit', async (e) => {
        e.preventDefault();

        const id = document.getElementById('paciente_id').value;

        const datos = {
            tipo_documento_id: document.getElementById('tipo_documento_id').value,
            numero_documento: document.getElementById('numero_documento').value,
            nombre1: document.getElementById('nombre1').value,
            nombre2: document.getElementById('nombre2').value,
            apellido1: document.getElementById('apellido1').value,
            apellido2: document.getElementById('apellido2').value,
            genero_id: document.getElementById('genero_id').value,
            correo: document.getElementById('correo').value,
            departamento_id: document.getElementById('departamento_id').value,
            municipio_id: document.getElementById('municipio_id').value,
        };

        const url = id ? `${API}/${id}` : API;
        const metodo = id ? 'PUT' : 'POST';

        const respuesta = await fetch(url, {
            method: metodo,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(datos),
        });

        const resultado = await respuesta.json();

        if (!respuesta.ok) {
            mostrarMensaje('Error: ' + Object.values(resultado.errors).flat().join(' | '), 'error');
            return;
        }

        mostrarMensaje(resultado.message, 'exito');
        document.getElementById('formPaciente').reset();
        document.getElementById('paciente_id').value = '';
        document.getElementById('btnCancelar').classList.add('hidden');
        cargarPacientes();
    });

    function mostrarMensaje(texto, tipo) {
        const div = document.getElementById('mensaje');
        div.textContent = texto;
        div.className = tipo === 'exito'
            ? 'mb-4 p-4 rounded bg-green-100 text-green-800'
            : 'mb-4 p-4 rounded bg-red-100 text-red-800';
        div.classList.remove('hidden');

        setTimeout(() => div.classList.add('hidden'), 4000);
    }

    // Cargar los datos de un paciente en el formulario para editarlo
    async function editarPaciente(id) {
        const paciente = await fetch(`${API}/${id}`).then(r => r.json());

        document.getElementById('paciente_id').value = paciente.id;
        document.getElementById('tipo_documento_id').value = paciente.tipo_documento_id;
        document.getElementById('numero_documento').value = paciente.numero_documento;
        document.getElementById('nombre1').value = paciente.nombre1;
        document.getElementById('nombre2').value = paciente.nombre2 ?? '';
        document.getElementById('apellido1').value = paciente.apellido1;
        document.getElementById('apellido2').value = paciente.apellido2 ?? '';
        document.getElementById('genero_id').value = paciente.genero_id;
        document.getElementById('correo').value = paciente.correo;
        document.getElementById('departamento_id').value = paciente.departamento_id;

        const municipios = await fetch(`/municipios/${paciente.departamento_id}`).then(r => r.json());
        llenarSelect('municipio_id', municipios);
        document.getElementById('municipio_id').value = paciente.municipio_id;

        document.getElementById('btnCancelar').classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Cancelar edición (limpiar formulario)
    document.getElementById('btnCancelar').addEventListener('click', () => {
        document.getElementById('formPaciente').reset();
        document.getElementById('paciente_id').value = '';
        document.getElementById('btnCancelar').classList.add('hidden');
    });

    // Eliminar un paciente
    async function eliminarPaciente(id) {
        const confirmar = confirm('¿Estás seguro de eliminar este paciente? Esta acción no se puede deshacer.');
        if (!confirmar) return;

        const respuesta = await fetch(`${API}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });

        const resultado = await respuesta.json();

        if (!respuesta.ok) {
            mostrarMensaje('Error al eliminar paciente', 'error');
            return;
        }

        mostrarMensaje(resultado.message, 'exito');
        cargarPacientes();
    }
    </script>
</x-app-layout>
# PRUEBA TÉCNICA - Sistema de Gestión de Proyectos

API REST para organizar el trabajo, gestionar plazos y rastrear el progreso mediante la estructuración de actividades en un modelo jerárquico: **Proyectos → Tareas → Subtareas**.

##  Tecnologías

- **Backend:** Laravel 11 (PHP 8.2+)
- **Autenticación:** Laravel Sanctum (Token Bearer)
- **Base de Datos:** MySQL / PostgreSQL / SQLite
- **Arquitectura:** RESTful API

##  Requisitos Previos

- PHP >= 8.2
- Composer
- MySQL 8+ / PostgreSQL 14+ / SQLite
- Git

## ⚡ Instalación Rápida

```bash
# 1. Clonar el repositorio
git clone https://github.com/Davishiio/SABGOB
cd sabgob

# 2. Instalar dependencias
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=sabgob
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Ejecutar migraciones y seeders
php artisan migrate --seed

# 6. Iniciar servidor de desarrollo
php artisan serve
```

El servidor estará disponible en: `http://127.0.0.1:8000`

##  Autenticación

La API utiliza **Laravel Sanctum** con tokens Bearer. Todas las rutas (excepto login/register) requieren autenticación.

### Registro
```http
POST /api/register
Content-Type: application/json

{
    "name": "Usuario Ejemplo",
    "email": "usuario@ejemplo.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "usuario@ejemplo.com",
    "password": "password123"
}
```

**Respuesta:** Retorna un `access_token` que debe incluirse en todas las solicitudes:
```http
Authorization: Bearer {token}
```

##  Endpoints de la API

### Proyectos
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/proyectos` | Listar proyectos del usuario |
| POST | `/api/proyectos` | Crear proyecto |
| GET | `/api/proyectos/{id}` | Ver proyecto |
| PUT | `/api/proyectos/{id}` | Actualizar proyecto |
| DELETE | `/api/proyectos/{id}` | Eliminar proyecto |
| GET | `/api/proyectos/{id}/completo` | Proyecto con tareas, subtareas y comentarios |

### Tareas
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/tareas` | Listar todas las tareas |
| GET | `/api/tareas?idProyecto={id}` | Filtrar tareas por proyecto |
| POST | `/api/tareas` | Crear tarea |
| GET | `/api/tareas/{id}` | Ver tarea |
| PUT | `/api/tareas/{id}` | Actualizar tarea |
| DELETE | `/api/tareas/{id}` | Eliminar tarea |
| GET | `/api/proyectos/{id}/tareas` | Tareas de un proyecto |

### Subtareas
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/api/subtareas` | Crear subtarea |
| PUT | `/api/subtareas/{id}` | Actualizar subtarea |
| DELETE | `/api/subtareas/{id}` | Eliminar subtarea |
| GET | `/api/tareas/{id}/subtareas` | Subtareas de una tarea |

### Comentarios
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/comentarios` | Listar mis comentarios |
| POST | `/api/comentarios` | Crear comentario |
| GET | `/api/comentarios/{id}` | Ver comentario |
| PUT | `/api/comentarios/{id}` | Editar comentario |
| DELETE | `/api/comentarios/{id}` | Eliminar comentario |

## Estructura de Datos

### Proyecto
```json
{
    "id": 1,
    "titulo": "Proyecto Demo",
    "descripcion": "Descripción del proyecto",
    "estado": "pendiente",
    "fecha_inicio": "2025-01-01",
    "fecha_limite": "2025-01-31",
    "has_comments": true
}
```

### Tarea
```json
{
    "id": 1,
    "idProyecto": 1,
    "titulo": "Tarea Demo",
    "descripcion": "Descripción de la tarea",
    "estado": "pendiente",
    "fecha_inicio": "2025-01-02",
    "fecha_limite": "2025-01-15",
    "has_comments": false
}
```

### Subtarea
```json
{
    "id": 1,
    "idTarea": 1,
    "titulo": "Subtarea Demo",
    "descripcion": "Descripción de la subtarea",
    "estado": "completado",
    "fecha_inicio": "2025-01-03",
    "fecha_limite": "2025-01-05",
    "has_comments": false
}
```

### Comentario
```json
{
    "id": 1,
    "idUsuario": 1,
    "cuerpo": "Contenido del comentario",
    "estado": "enviado",
    "tipoComentario": "Proyecto",
    "idComentario": 1
}
```

##  Seguridad

- **Autenticación por Token:** Todas las rutas protegidas requieren token Bearer.
- **Autorización por Propiedad:** Los usuarios solo pueden acceder a sus propios proyectos, tareas y subtareas.
- **Validación de Entrada:** Todas las solicitudes son validadas antes de procesarse.
- **Hash de Contraseñas:** Las contraseñas se almacenan hasheadas con bcrypt.

##  Usuarios de Prueba

Después de ejecutar `php artisan migrate --seed`:

| Email | Contraseña | Rol |
|-------|------------|-----|
| usuario@test.com | password | Usuario |

##  Estructura del Proyecto

```
app/
├── Http/Controllers/Api/
│   ├── AuthController.php        # Autenticación
│   ├── ProyectoController.php    # CRUD Proyectos
│   ├── TareaController.php       # CRUD Tareas
│   ├── SubtareaController.php    # CRUD Subtareas
│   └── ComentarioController.php  # CRUD Comentarios
├── Models/
│   ├── User.php
│   ├── Proyecto.php
│   ├── Tarea.php
│   ├── Subtarea.php
│   └── Comentario.php
└── Providers/
    └── AppServiceProvider.php    # Morph Map Configuration

database/
├── migrations/                   # Esquema de BD
└── seeders/                      # Datos de prueba

routes/
└── api.php                       # Definición de rutas API
```


Este proyecto fue desarrollado como parte de un proceso de selección técnica.

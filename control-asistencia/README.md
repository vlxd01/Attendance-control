# Control de Asistencia — Grupo Angry Birdgos

Proyecto del curso **Desarrollo Web** · Universidad Francisco Gavidia (UFG).

Sistema web hecho en **Laravel 12** para llevar el control de asistencia de estudiantes por curso:

- **Estudiantes**: registrar, editar, eliminar y buscar (carnet único con formato `RR123456`).
- **Cursos**: registrar, editar, eliminar e **inscribir estudiantes** (relación muchos a muchos).
- **Tomar asistencia**: elegir curso y fecha, marcar a cada estudiante como *Presente*, *Ausente*, *Tarde* o *Justificado* (con observación opcional). Si la fecha ya fue tomada, se carga para **editarla**.
- **Justificaciones**: estado *Justificado* para faltas con permiso; el **motivo** se escribe en la columna `observacion` y es **obligatorio** solo en ese caso (`required_if`). Los justificados se cuentan aparte de los ausentes en reportes, CSV, dashboard y consulta.
- **Consulta por fecha**: ver todas las asistencias de un día (curso opcional) con conteo de presentes, ausentes, tarde y justificados.
- **Reportes**: por curso (con rango de fechas opcional) con totales y **% de asistencia** por estudiante, exportación a **CSV** (Excel) e **historial por estudiante**.
- **Portal del estudiante**: el estudiante entra con su **carnet y contraseña**, marca su **entrada** y su **salida** del día y ve su historial con las horas totales.
- **Marcaciones (docente)**: tabla de entradas y salidas filtrable por fecha (por defecto hoy) y por estudiante.
- **Inicio (dashboard)** con conteos generales (incluye *entradas de hoy*) y últimas clases registradas.
- **Inicio de sesión** con dos accesos: **Docente** (correo) y **Estudiante** (carnet). Cada uno solo puede entrar a su propia área.
- Diseño **“Institucional fresco”** (Opción 4): menú lateral, tipografía Public Sans y colores verde azulado.

> Sin npm, sin Vite, sin frameworks de JavaScript: solo Laravel + Blade + CSS propio (`public/css/app.css`).

---

## Requisitos

| Programa | Versión |
|---|---|
| XAMPP (Apache + MariaDB/MySQL + PHP) | PHP **8.2 o superior** (MariaDB 10.4 funciona) |
| Composer | 2.x |

Extensiones de PHP necesarias (vienen activadas en XAMPP): `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `zip`.
Si `composer install` se queja de alguna, abra `C:\xampp\php\php.ini` y quite el `;` de la línea `extension=...` correspondiente.

---

## Instalación en Windows con XAMPP

1. **Iniciar XAMPP**: abrir el *XAMPP Control Panel* y dar **Start** a **MySQL** (Apache no es necesario).

2. **Crear la base de datos** `control_asistencia`:
   - Con **phpMyAdmin**: entrar a <http://localhost/phpmyadmin> → *Nueva* → nombre `control_asistencia` → cotejamiento `utf8mb4_unicode_ci` → *Crear*.
   - O con **MySQL Workbench** / consola:
     ```sql
     CREATE DATABASE control_asistencia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
     ```

3. **Descomprimir el proyecto** (por ejemplo en `C:\proyectos\control-asistencia`) y abrir una terminal (CMD o PowerShell) en esa carpeta.

4. **Instalar dependencias** (crea la carpeta `vendor`):
   ```bash
   composer install
   ```

5. **Crear el archivo de configuración** `.env` a partir del ejemplo:
   ```bash
   copy .env.example .env
   ```
   Ya viene configurado para XAMPP (`root` sin contraseña):
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=control_asistencia
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   > Si su MySQL tiene contraseña, escríbala en `DB_PASSWORD`.

6. **Generar la llave de la aplicación**:
   ```bash
   php artisan key:generate
   ```

7. **Crear las tablas y cargar datos de ejemplo**:
   ```bash
   php artisan migrate --seed
   ```
   (Para empezar de cero en cualquier momento: `php artisan migrate:fresh --seed`)

8. **Levantar el servidor**:
   ```bash
   php artisan serve
   ```
   Abrir <http://127.0.0.1:8000>

> Si `php` no se reconoce en la terminal, use la ruta completa: `C:\xampp\php\php.exe artisan serve`, o agregue `C:\xampp\php` a la variable de entorno PATH.

### Credenciales

| Acceso | Usuario | Contraseña |
|---|---|---|
| Docente (pestaña *Docente*) | `admin@asistencia.com` | `password` |
| Estudiante (pestaña *Estudiante*) | carnet `RH100124` (o cualquier carnet del seeder, ej. `MC204523`) | `estudiante123` |

> Todos los estudiantes del seeder tienen la contraseña `estudiante123`. El docente puede asignar o cambiar la contraseña de cada estudiante en **Estudiantes → Editar** (si el campo se deja vacío, se conserva la anterior).

### Datos de ejemplo (seeder)

- 4 cursos: DAW-01, BD-02, PRG-03, RED-01.
- 15 estudiantes con nombres salvadoreños y carnets tipo `RH100124`.
- Inscripciones en los 4 cursos.
- Asistencias de las últimas 6 clases (lunes y miércoles) en DAW-01 y BD-02.
- Marcaciones de entrada/salida de los últimos 5 días hábiles, y 5 estudiantes que ya marcaron entrada **hoy**.
  `RH100124` queda **sin marcar hoy** para probar el botón “Marcar entrada” en la demostración.

### Actualizar una instalación anterior (PC donde ya estaba el proyecto)

Copie los archivos nuevos encima del proyecto (sin borrar `vendor` ni `.env`) y ejecute:

```bash
php artisan migrate:fresh --seed
php artisan optimize:clear
```

`migrate:fresh --seed` borra y vuelve a crear todo con los datos de ejemplo (contraseñas de estudiantes incluidas).
Si prefiere **conservar sus datos**, use `php artisan migrate` (agrega la columna `password` y la tabla `marcaciones`); los estudiantes que ya existían quedarán **sin contraseña** hasta que el docente se la asigne en *Estudiantes → Editar*.

### Pruebas automáticas

```bash
php artisan test
```
Las pruebas usan SQLite en memoria (configurado en `phpunit.xml`), así que **no tocan** la base de datos real.
Si fallan por “could not find driver”, active `extension=pdo_sqlite` y `extension=sqlite3` en `php.ini`.

---

## Guía para exponer

### 1. ¿Cómo funciona Laravel? (MVC)

Laravel usa el patrón **MVC (Modelo – Vista – Controlador)**:

1. El navegador pide una URL → **Ruta** (`routes/web.php`) decide qué controlador atiende.
2. El **Controlador** (`app/Http/Controllers`) valida datos y usa los **Modelos**.
3. El **Modelo** (`app/Models`) representa una tabla y habla con la base de datos (Eloquent ORM).
4. El controlador devuelve una **Vista** Blade (`resources/views`) con el HTML.

### 2. Estructura de carpetas importante

```
app/
  Http/Controllers/
    LoginController.php       → iniciar / cerrar sesión (docente y estudiante)
    DashboardController.php   → página de inicio con conteos
    EstudianteController.php  → CRUD de estudiantes
    CursoController.php       → CRUD de cursos + inscripciones
    AsistenciaController.php  → tomar / editar asistencia
    ReporteController.php     → reporte por curso, CSV e historial
    PortalEstudianteController.php → panel del estudiante, marcar entrada y salida
    MarcacionController.php   → tabla de marcaciones para el docente
  Models/
    Estudiante.php  Curso.php  Asistencia.php  Marcacion.php  User.php
config/auth.php → guards "web" (docentes) y "estudiante" (estudiantes)
database/
  migrations/   → estructura de las tablas
  seeders/DatabaseSeeder.php → datos de ejemplo
lang/es/        → mensajes de validación en español
public/css/app.css → estilos
resources/views/
  layouts/app.blade.php        → plantilla del docente (menú, mensajes, pie)
  layouts/estudiante.blade.php → plantilla del portal del estudiante
  auth/ estudiantes/ cursos/ asistencias/ reportes/ marcaciones/ estudiante/ dashboard.blade.php
routes/web.php  → todas las rutas
tests/Feature/  → pruebas automáticas
```

### 3. Base de datos y relaciones

```
estudiantes (id, carnet ÚNICO, nombres, apellidos, correo ÚNICO, password)
cursos      (id, codigo ÚNICO, nombre, docente, descripcion)
curso_estudiante (id, curso_id, estudiante_id)          ← tabla pivote (inscripciones)
asistencias (id, curso_id, estudiante_id, fecha, estado, observacion)
             ÚNICO (curso_id + estudiante_id + fecha)
marcaciones (id, estudiante_id, fecha, hora_entrada, hora_salida)
             ÚNICO (estudiante_id + fecha)                ← una marcación por día
users       (docentes que inician sesión)
```

| Relación | Tipo | Código |
|---|---|---|
| Curso ↔ Estudiante | Muchos a muchos | `belongsToMany` (tabla `curso_estudiante`) |
| Curso → Asistencias | Uno a muchos | `hasMany` |
| Estudiante → Asistencias | Uno a muchos | `hasMany` |
| Asistencia → Curso / Estudiante | Pertenece a | `belongsTo` |
| Estudiante → Marcaciones | Uno a muchos | `hasMany` |
| Marcación → Estudiante | Pertenece a | `belongsTo` |

### 4. Rutas principales

| Método | URL | Qué hace |
|---|---|---|
| GET/POST | `/login` | Formulario e inicio de sesión del docente (`/login?tipo=estudiante` muestra la pestaña Estudiante) |
| POST | `/logout` | Cerrar sesión del docente |
| POST | `/estudiante/login` | Inicio de sesión del estudiante (carnet + contraseña) |
| GET | `/estudiante` | Panel del estudiante |
| POST | `/estudiante/entrada` · `/estudiante/salida` | Marcar entrada / salida |
| POST | `/estudiante/salir` | Cerrar sesión del estudiante |
| GET | `/marcaciones?fecha=&estudiante_id=` | Marcaciones (vista del docente) |
| GET | `/` | Dashboard |
| (resource) | `/estudiantes` | index, create, store, show, edit, update, destroy |
| (resource) | `/cursos` | igual que estudiantes |
| PUT | `/cursos/{curso}/inscripciones` | Guardar inscripciones del curso |
| GET | `/asistencias?curso_id=&fecha=` | Lista para tomar asistencia |
| POST | `/asistencias` | Guardar asistencia |
| GET | `/reportes?curso_id=&desde=&hasta=` | Reporte por curso |
| GET | `/reportes/csv?...` | Descargar CSV |
| GET | `/reportes/estudiante/{id}` | Historial del estudiante |

Ver todas con: `php artisan route:list`

- Las rutas del docente están en `Route::middleware('auth:web')` y las del estudiante en `Route::middleware('auth:estudiante')`.
- Sin sesión redirigen a `/login`. Un estudiante que intenta abrir una página del docente (o al revés) también es enviado al login, y como ya tiene sesión, el login lo devuelve a **su propio panel** (configurado en `bootstrap/app.php`).

### 5. Flujo de “Tomar asistencia” (lo más importante)

1. El docente elige **curso** y **fecha** (formulario GET).
2. `AsistenciaController@index` busca los **estudiantes inscritos** y, si ya existe asistencia en esa fecha, la carga para editarla.
3. Cada fila tiene radios con `name="estados[ID_ESTUDIANTE]"`, así se envía un **arreglo** `id => estado`.
4. `AsistenciaController@guardar` valida y recorre el arreglo usando **`updateOrCreate`**:
   - si ya existe (curso + estudiante + fecha) → **actualiza**;
   - si no existe → **crea**.
   Por eso nunca hay registros duplicados (además del índice único en la tabla).

### 6. Portal del estudiante: marcar entrada y salida

1. El estudiante entra en la pestaña **Estudiante** del login con carnet + contraseña. `LoginController@ingresarEstudiante` usa `Auth::guard('estudiante')->attempt(...)`, que busca en la tabla `estudiantes` (el modelo `Estudiante` extiende `Authenticatable`).
2. `PortalEstudianteController@panel` busca la marcación de **hoy** y muestra **solo el botón que corresponde**:
   - sin marcación → **Marcar entrada**;
   - con entrada y sin salida → **Marcar salida**;
   - con las dos → “Jornada completa”.
3. Aunque alguien envíe el formulario a mano, el **servidor valida**:
   - no hay doble entrada (“Ya marcaste tu entrada hoy.”);
   - no hay salida sin entrada (“Primero debes marcar tu entrada.”);
   - no hay doble salida (“Ya marcaste tu salida hoy.”).
   Además, la tabla tiene un índice **único** `estudiante_id + fecha`.
4. Las horas se calculan en el modelo `Marcacion` (`getHorasAttribute`): minutos entre entrada y salida → “4 h 30 min”.
5. La hora es la de El Salvador porque `config/app.php` tiene `'timezone' => 'America/El_Salvador'`.

### 7. Cálculo del reporte

```
% asistencia = (presentes + tardes) / total de clases registradas × 100
```
La llegada tarde cuenta como asistencia. El **justificado** cuenta en el total pero **no** como asistencia (el estudiante no estuvo en clase); se muestra en su propia columna para no confundirlo con una falta sin permiso. Si el porcentaje es menor a 75 % se muestra en rojo.

### 8. Posibles preguntas del ingeniero

**¿Por qué es Laravel y no PHP puro?**
Usamos rutas de Laravel, controladores, modelos Eloquent, migraciones, seeders, validación, middleware `auth`, vistas Blade y pruebas con PHPUnit. Todo eso es propio de Laravel.

**¿Qué es una migración?**
Un archivo PHP que describe la estructura de una tabla. Con `php artisan migrate` se crean las tablas sin escribir SQL a mano, y así todo el equipo tiene la misma base de datos.

**¿Qué es un seeder?**
Una clase que inserta datos de prueba (`php artisan db:seed` o `migrate --seed`).

**¿Qué es Eloquent?**
El ORM de Laravel: cada tabla tiene un modelo y trabajamos con objetos (`Estudiante::create(...)`, `$curso->estudiantes`) en lugar de escribir SQL.

**¿Qué es `$fillable`?**
La lista de campos que se permiten llenar con `create()` o `update()`. Protege contra la *asignación masiva* de campos no deseados.

**¿Cómo funciona la relación muchos a muchos?**
Un estudiante puede estar en muchos cursos y un curso tiene muchos estudiantes. Se usa la tabla pivote `curso_estudiante` y el método `belongsToMany`. Para inscribir usamos `sync()`, que deja inscritos solo a los estudiantes marcados (agrega y quita automáticamente).

**¿Qué hace `Route::resource`?**
Crea en una sola línea las 7 rutas del CRUD (index, create, store, show, edit, update, destroy).

**¿Cómo se valida?**
Con `$request->validate([...])` en el controlador. Si falla, Laravel regresa al formulario con los errores y los datos escritos (`old()`). Los mensajes están en español en `lang/es/validation.php` porque `APP_LOCALE=es`.

**¿Cómo se evita que se repita el carnet?**
Con la regla `unique` en la validación y con `->unique()` en la migración (doble protección). Al editar se usa `Rule::unique(...)->ignore($estudiante)` para no chocar con el propio registro.

**¿Cómo funciona el login?**
`Auth::attempt($credenciales)` busca el usuario por correo y compara la contraseña encriptada con **bcrypt**. Si es correcta crea la sesión. El middleware `auth` protege las demás rutas.

**¿Cómo inicia sesión un estudiante si no es un `User`?**
En `config/auth.php` agregamos un segundo *guard* llamado `estudiante` con su *provider* que usa el modelo `Estudiante`. El modelo extiende `Authenticatable` (igual que `User`), y en el controlador usamos `Auth::guard('estudiante')->attempt(['carnet' => ..., 'password' => ...])`.

**¿Cómo se guarda la contraseña del estudiante?**
Encriptada con bcrypt. El modelo tiene el *cast* `'password' => 'hashed'`, así que Laravel la encripta sola al guardar, y `$hidden` evita que se muestre.

**¿Qué pasa si el estudiante aprieta dos veces “Marcar entrada”?**
La segunda vez el controlador encuentra la marcación de hoy y regresa con el error “Ya marcaste tu entrada hoy.”. También lo impide el índice único de la tabla.

**¿Qué es `@csrf`?**
Un token oculto que Laravel exige en formularios POST/PUT/DELETE para evitar ataques CSRF (que otro sitio envíe formularios en nombre del usuario).

**¿Qué es `@method('PUT')` / `@method('DELETE')`?**
Los formularios HTML solo soportan GET y POST; Laravel “simula” PUT y DELETE con un campo oculto.

**¿Qué pasa si elimino un estudiante o un curso?**
Las llaves foráneas tienen `cascadeOnDelete()`, por lo que también se borran sus inscripciones y asistencias.

**¿Qué son los mensajes flash?**
Mensajes que se guardan en la sesión solo para la siguiente petición: `redirect()->with('ok', 'Guardado')` y en el layout se muestran con `session('ok')`.

**¿Por qué `Schema::defaultStringLength(191)`?**
Para compatibilidad con versiones antiguas de MySQL/MariaDB (como la de algunos XAMPP), que fallan al crear índices únicos en columnas de texto largas con `utf8mb4`.

**¿Cómo se prueban?**
`php artisan test` ejecuta pruebas automáticas (login de docente y estudiante, separación de áreas, CRUD, toma de asistencia, marcaciones y reportes) usando una base SQLite en memoria.

### 9. Relación con el avance anterior del grupo

El primer avance (PHP puro, sin Laravel) tenía: tabla `estudiantes` con **carnet de 8 caracteres** (ej. `AC100724`) y una vista para buscar al estudiante por carnet y marcar entrada/salida. En esta versión se respetó el **carnet único de 8 caracteres** (2 letras + 6 números), el nombre de la base de datos `control_asistencia` y la zona horaria de El Salvador, y se rehízo todo en Laravel agregando cursos, inscripciones, estados de asistencia y reportes. La idea de **marcar entrada/salida con el carnet** ahora vive en el **portal del estudiante** (tabla `marcaciones`), con contraseña y validaciones en el servidor.

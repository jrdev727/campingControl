# 🚀 Guía de Instalación - Camping Sonrisas

## 📋 Requisitos del Servidor

- PHP 7.4 o superior
- MySQL 5.7 o superior / MariaDB 10.3 o superior
- Extensiones PHP requeridas:
  - mysqli
  - pdo_mysql
  - json
  - session

## 🗄️ Paso 1: Configurar la Base de Datos

### Opción A: Via phpMyAdmin (recomendado para principiantes)

1. Accede a phpMyAdmin en tu hosting
2. Ve a la pestaña "Importar"
3. Selecciona el archivo `database_setup.sql`
4. Haz clic en "Continuar"

### Opción B: Via línea de comandos

```bash
mysql -u tu_usuario -p < database_setup.sql
```

## 🔧 Paso 2: Configurar la Conexión

1. Abre el archivo `php/conexion.php`
2. Modifica estos valores según los datos de tu hosting:

```php
$servername = "localhost";          // Generalmente "localhost"
$username = "tu_usuario_mysql";     // Usuario de MySQL
$password = "tu_contraseña_mysql";  // Contraseña de MySQL
$dbname = "camping_sonrisas";       // Nombre de la base de datos
```

### Datos comunes según hosting:

**InfinityFree:**
```php
$servername = "sql123.infinityfreeapp.com";
$username = "epiz_xxxxx";
$password = "tu_password";
$dbname = "epiz_xxxxx_camping";
```

**000webhost:**
```php
$servername = "localhost";
$username = "id12345_usuario";
$password = "tu_password";
$dbname = "id12345_camping";
```

**Hostinger:**
```php
$servername = "localhost";
$username = "u123456789_usuario";
$password = "tu_password";
$dbname = "u123456789_camping";
```

## 📁 Paso 3: Subir Archivos

Sube estos archivos y carpetas a tu servidor (vía FTP o File Manager):

```
📂 public_html/ (o la carpeta raíz de tu hosting)
├── 📄 admin.php
├── 📄 index.php
├── 📄 login.html
├── 📄 logout.php
├── 📄 reportes.php
├── 📂 css/
│   └── styles.css
├── 📂 js/
│   ├── app.js
│   ├── pos.js
│   └── reportes.js
├── 📂 includes/
│   ├── header.php
│   └── sidebar.php
└── 📂 php/
    ├── anular_entrada.php
    ├── conexion.php ⚠️ (editado con tus datos)
    ├── control_acceso.php
    ├── dashboard_stats.php
    ├── login.php
    ├── obtener_entradas_hoy.php
    ├── reporte_financiero.php
    ├── reporte_pdf.php
    ├── reportes.php
    └── reportes_entradas.php
```

## 🔐 Paso 4: Acceder al Sistema

1. Abre tu navegador
2. Ve a: `https://tu-dominio.com/login.html`
3. Usa estas credenciales por defecto:

**Administrador:**
- Usuario: `admin`
- Contraseña: `admin123`

**Empleado:**
- Usuario: `empleado`
- Contraseña: `emp123`

⚠️ **IMPORTANTE:** Cambia estas contraseñas después del primer acceso

## 🛠️ Solución de Problemas

### Error: "No se puede conectar a la base de datos"
- Verifica que los datos en `php/conexion.php` sean correctos
- Asegúrate de que la base de datos existe
- Verifica que el usuario tiene permisos en esa base de datos

### Error: "404 Not Found"
- Verifica que subiste los archivos a la carpeta correcta
- Algunos hostings usan `public_html`, otros `www` o `htdocs`

### Error: "500 Internal Server Error"
- Revisa los logs de error de PHP en tu hosting
- Verifica que las extensiones PHP requeridas estén activas
- Asegúrate de que los permisos de archivos sean correctos (644 para archivos, 755 para carpetas)

### La página está en blanco
- Activa el modo de error en desarrollo temporal:
  En `php/conexion.php` agrega al inicio:
  ```php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ```

## 📊 Verificación de Instalación

Crea un archivo `test_conexion.php` en la raíz:

```php
<?php
require_once 'php/conexion.php';

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

echo "✅ Conexión exitosa a la base de datos!<br>";
echo "📊 Base de datos: " . $dbname . "<br>";
echo "🌐 Servidor: " . $servername . "<br>";

// Verificar tablas
$result = $conn->query("SHOW TABLES");
echo "<br>📋 Tablas encontradas:<br>";
while ($row = $result->fetch_array()) {
    echo "  - " . $row[0] . "<br>";
}

$conn->close();
?>
```

Accede a `https://tu-dominio.com/test_conexion.php`

**¡Elimina este archivo después de verificar!**

## 🎯 Próximos Pasos

1. Cambia las contraseñas por defecto
2. Configura la ubicación del clima en `admin.php` (línea 294)
3. Realiza pruebas de registro de entradas
4. Verifica que los reportes funcionen correctamente

## 📞 Soporte

Si tienes problemas, revisa:
1. Los logs de error de tu hosting
2. La consola del navegador (F12)
3. Que todos los archivos se subieron correctamente

# CASFID – Reto Técnico Backend (DailyTrends)

¡Bienvenido/a al reto técnico de CASFID!

Este reto evalúa tus habilidades técnicas en Symfony, diseño limpio, scraping y trabajo con MongoDB/MySQL.  
El proyecto se llama **DailyTrends** y actúa como un agregador de noticias de portada.

---

## Objetivo general

El objetivo es desarrollar una API en Symfony 7+ que recoja y gestione noticias de portada de _El País_ y _El Mundo_ mediante **scraping**, y permita gestionarlas (lectura, creación manual, edición y borrado) vía API REST.

**Duración estimada total**: 5 días  
Según indicación del reclutador, realizarás únicamente la Parte 1 (1 día), las Partes 1 y 2 (3 días), o el reto completo (5 días).

---

## Partes del reto

### Parte 1 – Web Scraping y almacenamiento: 1 día

- Obtener las 5 noticias principales de hoy de El País y El Mundo (sin usar RSS).
- Guardarlas automáticamente en MongoDB/MySQL (con o sin ODM/ORM).
- Implementar arquitectura limpia (controladores, servicios, repositorios, documentos).
- Polimorfismo obligatorio: cada periódico tendrá su propio scraper.
- Manejo de errores robusto.

### Parte 2 – API REST CRUD: +2 días (total: 3 días)

Implementar los endpoints para gestionar noticias (`Feed`):

- `GET /feeds`
- `GET /feeds/{id}`
- `POST /feeds`
- `PUT /feeds/{id}`
- `DELETE /feeds/{id}`

Requisitos:

- Validación de datos
- Separación de responsabilidades
- Buenas prácticas (SOLID, inyección de dependencias, DTOs…)

### Parte 3 – Tests, Documentación y Arquitectura: +2 días (total: 5 días)

- Pruebas unitarias (scrapers, repositorios…)
- Pruebas funcionales (endpoints)
- Documentación Swagger/OpenAPI
- Diagrama simple de arquitectura
- `README.md` con instrucciones, arquitectura y tests

---

## Entorno Docker incluido

El proyecto ya incluye una configuración **Docker lista para usar**, con PHP 8.4, Nginx, Symfony CLI y Node.js.

Además, tienes la opción de elegir entre **MySQL** y **MongoDB** que ya están configurados en Docker:

- **MySQL**: Accesible a través de PHPMyAdmin en el puerto 8889
    - Usuario phpMyAdmin: `root`
    - Contraseña: `password`
- **MongoDB**: Accesible a través de Mongo Express en el puerto 8081
  - Usuario de Mongo Express: `admin`
  - Contraseña: `pass`

Solo debes definir las variables de entorno `UID` y `UNAME` según tu propia configuración local.
De esta forma compartirás los mismos permisos al utilizar Symfony CLI dentro del contenedor de Docker.


## Criterios de Evaluación

- **Limpieza y claridad del código**
- **Modularidad y mantenimiento a largo plazo**
- **Uso adecuado de Symfony y sus componentes**
- **Calidad y resiliencia del scraping**
- **Diseño orientado a objetos y uso de patrones**
- **Desacoplamiento de componentes**
- **Cobertura de tests y calidad de la documentación**

---

## Recomendación técnica

Presta especial atención al control de errores, al desacoplamiento y con la vista siempre puesta en el rendimiento y escalabilidad.

---

## Entrega

- Sube el proyecto a un **repositorio público** (GitHub, GitLab, etc.).
- Realiza **commits descriptivos y frecuentes**, documentando cada avance.
- Comparte el enlace con el equipo técnico de **CASFID**.

---
## Architecture & Design Decisions

### Cursor-based pagination

A cursor-based pagination approach has been chosen for the feeds endpoint.

While this decision may be considered overkill for the current scope of the project, it provides a more robust and scalable solution in case the dataset grows significantly. Cursor-based pagination avoids common issues of offset-based pagination such as duplicated or missing records when new items are inserted.

This approach ensures stable pagination and better performance for large datasets.

---

### Doctrine mapping without custom Types

Doctrine custom Types were intentionally not used for domain Value Objects.

Instead, persistence relies on:
- Primitive database types
- Getter methods on the domain model returning scalar values
- `__toString()` implementations in Value Objects

This decision implies a minor compromise in strict DDD separation between layers, but it was made consciously for the following reasons:

1. At the current stage of the project, there is no domain logic that requires operating directly on the Value Objects outside the aggregate.
2. Using `__toString()` is a common and accepted practice for Value Objects based on a single primitive value (such as strings), keeping the implementation simple and readable.
3. This approach reduces Doctrine configuration complexity while maintaining a clear and expressive domain model.

This decision can be revisited in the future if the domain evolves and requires richer Value Object behavior.

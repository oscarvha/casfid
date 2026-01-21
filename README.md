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


## 🧠 Decisiones de Diseño y Arquitectura - ES

### 📐 Arquitectura General

La aplicación está estructurada en tres capas principales:

- **Domain**  
  Contiene el núcleo del negocio: entidades, value objects, colecciones y contratos (interfaces).  
  Esta capa no depende de ningún framework ni detalle técnico.

- **Application**  
  Implementa los casos de uso de la aplicación.  
  Orquesta el dominio a través de interfaces, sin conocimiento de cómo se persisten o exponen los datos.

- **Infrastructure**  
  Contiene los detalles técnicos: controladores HTTP, repositorios Doctrine, listeners, configuración de seguridad, etc.  
  Actúa como adaptador entre el mundo exterior y la aplicación.

Las dependencias apuntan siempre hacia el dominio, cumpliendo el principio fundamental de la arquitectura hexagonal.

---

### 🔌 Arquitectura Hexagonal (Ports & Adapters)

El sistema sigue el patrón **Ports & Adapters**:

- Los **ports** se definen como interfaces en el dominio o la aplicación (por ejemplo, repositorios).
- Los **adapters** viven en la infraestructura (por ejemplo, implementaciones Doctrine o controladores HTTP).

Esto permite sustituir tecnologías sin afectar al dominio, facilita el testeo y evita acoplamientos innecesarios con el framework.

---

### 📦 Domain-Driven Design (DDD)

Se ha aplicado **DDD táctico** de forma deliberada y proporcionada:

- El dominio modela conceptos explícitos del negocio.
- Se utilizan **Value Objects** inmutables.
- Los repositorios se definen como contratos, no como implementaciones técnicas.

No se han introducido patrones avanzados de DDD (Domain Events, Aggregates complejos) al no ser necesarios para el alcance actual del proyecto.

---

### 📄 Paginación basada en Cursor

Se ha optado por un sistema de **paginación basada en cursor** en lugar de offset tradicional.

Aunque puede considerarse una solución más compleja para el tamaño actual del proyecto, esta decisión facilita la escalabilidad futura y evita problemas habituales del paginado por offset en grandes volúmenes de datos.

El cursor se basa en el identificador del último elemento recuperado.

---

### 🧱 Value Objects y Persistencia

No se han definido **Doctrine Custom Types** para los Value Objects.

En su lugar:
- El dominio expone los valores mediante getters que devuelven tipos primitivos.
- Los Value Objects implementan `__toString()` cuando representan un valor textual único.

Esta decisión se basa en que:
1. Actualmente el dominio no necesita operar internamente con los Value Objects.
2. `__toString()` es un patrón común y aceptado para este tipo de objetos.
3. Se evita acoplar el dominio a detalles de persistencia.

Esta elección supone un compromiso consciente entre pureza teórica y simplicidad práctica.

---

### ⚖️ Enfoque Pragmático

El objetivo de la arquitectura es ofrecer:
- Separación clara de responsabilidades
- Facilidad de testeo y evolución
- Decisiones justificadas y documentadas

Cada elección arquitectónica está pensada para resolver problemas reales del proyecto, evitando la sobre-ingeniería.


## 🧠 Design Decisions and Architecture - ENG

This project follows a **pragmatic approach** to **Domain-Driven Design (DDD)** and **Hexagonal Architecture (Ports & Adapters)**, prioritizing clarity, maintainability, and scalability while avoiding unnecessary complexity.

---

### 📐 General Architecture

The application is structured into three main layers:

- **Domain**  
  Contains the core business logic: entities, value objects, collections, and contracts (interfaces).  
  This layer is framework-agnostic and has no technical dependencies.

- **Application**  
  Implements application use cases.  
  It orchestrates domain logic through interfaces without knowing how data is persisted or exposed.

- **Infrastructure**  
  Contains technical details such as HTTP controllers, Doctrine repositories, event listeners, and security configuration.  
  Acts as an adapter between the outside world and the application.

All dependencies point inward toward the domain, respecting the core principle of hexagonal architecture.

---

### 🔌 Hexagonal Architecture (Ports & Adapters)

The system follows the **Ports & Adapters** pattern:

- **Ports** are defined as interfaces in the Domain or Application layers (e.g. repositories).
- **Adapters** are implemented in the Infrastructure layer (e.g. Doctrine repositories, HTTP controllers).

This approach allows replacing technologies without affecting the domain, improves testability, and reduces coupling to the framework.

---

### 📦 Domain-Driven Design (DDD)

A **tactical DDD** approach has been applied deliberately and proportionally:

- The domain models explicit business concepts.
- **Value Objects** are immutable.
- Repositories are defined as contracts, not technical implementations.

Advanced DDD patterns (such as domain events or complex aggregates) were intentionally avoided as they are not required for the current scope.

---

### 📄 Cursor-Based Pagination

A **cursor-based pagination** strategy was chosen instead of traditional offset-based pagination.

Although this may be considered overkill for the current size of the project, it provides better scalability and avoids common issues related to offset pagination when dealing with large datasets.

The cursor is based on the identifier of the last retrieved item.

---

### 🧱 Value Objects and Persistence

**Doctrine Custom Types** were intentionally not used for Value Objects.

Instead:
- The domain exposes primitive values via getters.
- Value Objects implement `__toString()` when they represent a single textual value.

This decision is based on the following considerations:
1. The domain currently does not need to operate internally on Value Objects.
2. `__toString()` is a common and accepted pattern for this type of object.
3. It avoids coupling the domain to persistence-specific concerns.

This represents a conscious trade-off between theoretical purity and practical simplicity.

---

### ⚖️ Pragmatic Approach

The architectural goal of this project is to provide:
- Clear separation of responsibilities
- Easy testability and evolution
- Explicit and documented design decisions

Each architectural choice is intended to solve real project needs while avoiding over-engineering.

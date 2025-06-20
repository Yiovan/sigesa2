create database sigesa;


/*aca cree la base de datos para su uso */

select * from usuarios;
select * from tareas;

delete * from tareas 
where id=8;
/*se crearon aca la tabla de usuarios para que se regirstren estos datos

- su identificador 
- nombre 
- correo
- fecha de registro del usuario*/
CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nombre TEXT NOT NULL,
    correo TEXT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT NOW()
);

/*se creo la tabla de tareas donde tienen estos datos de referencia
- id 
- titulo de la tarea 
- descripcion simple de la tarea*/

CREATE TABLE tareas (
    id SERIAL PRIMARY KEY,
    titulo TEXT NOT NULL,
    descripcion TEXT,
    estado TEXT CHECK (estado IN ('pendiente', 'completada')) DEFAULT 'pendiente',
    fecha_creacion TIMESTAMP DEFAULT NOW(),
    usuario_id INTEGER NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);





-- Insert de ejemplo (opcional)
INSERT INTO usuarios (nombre, correo) VALUES 
('Juan Pérez', 'juan@example.com'),

('Milner Mochet', 'milnermochet@example.com')

INSERT INTO tareas (titulo, descripcion, estado, usuario_id) VALUES 
('Primera tarea', 'Descripción de la primera tarea', 'pendiente', 1),
('Segunda tarea', 'Otra descripción', 'completada', 2);

/*Consulta todas la tabla de usuarios, selecciona los datos de esa persona*/
select nombre, date(fecha_registro) as fecha from usuarios 
WHERE fecha_registro >= NOW() - INTERVAL '30 days';

UPDATE usuarios
SET nombre = 'Giovanni Cabrera'
WHERE id = 1;

update usuarios 
set nombre = 'Milena Martinez'
where id = 2;

update usuarios 
set nombre = 'Ruben'
where id = 3;

/*esto lo que hace es eliminar los usuarios que pasan de un año*/
DELETE FROM usuarios
WHERE fecha_registro < NOW() - INTERVAL '1 year';

-- el usuario dev es para acceder a la pagina en general
CREATE USER dev WITH PASSWORD '12345678';
GRANT ALL PRIVILEGES ON DATABASE sigesa TO dev;
-- lo que hace es llamar los permisos para que se puedan insertar seleccionar eliminar y actualizar
GRANT SELECT, INSERT, UPDATE, DELETE ON tareas TO dev;
GRANT USAGE, SELECT ON SEQUENCE tareas_id_seq TO dev;

/*consulta extra para verificar como seria la union completa de la db*/
select * from tareas t
join usuarios u
on u.id= t.id;



-- Permisos para la tabla usuarios
GRANT SELECT, INSERT, UPDATE, DELETE ON usuarios TO dev;
GRANT USAGE, SELECT ON SEQUENCE usuarios_id_seq TO dev;

-- Permisos para la tabla tareas
GRANT SELECT, INSERT, UPDATE, DELETE ON tareas TO dev;
GRANT USAGE, SELECT ON SEQUENCE tareas_id_seq TO dev;


//
1)Instalar el plugin
2)Crear curso "Quinto Año Ingenieria Civil 2015" y dejar como profesor al administrador
3)Add new activity (Url)
3.1)En Content poner ubicacion del archivo index.php; en mi caso http://localhost/moodle/local/gia/index.php
3.2)En URL Variables escribir "courseid" y seleccionar variable id
3.3) Save and display
//
1)Cargar en la tabla mdl_course_status(desde phpmyadmin) el archivo mdl_course_status(2). Este archivo trae el historial de ramos 
aprobados por los alumnos con id 3,4,5,6. Si ya tienen alumnos creados utilicen los que tengan estas ids para que el codiglo los lea.
La columna Status toma valores 0(ramo no aprobado),1(ramo aprobado),2(cursando ramo).
2)Si no tienen rellenada la tabla mdl_user_info , pueden cargar el archivo mdl_user_info(1) en la base de datos.
3)Enrolar a alumnos en el curso de Quinto Año como Students.


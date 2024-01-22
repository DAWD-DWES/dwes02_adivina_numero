<?php
/**
 * Recupera un usuario dado su nombre y password
 * 
 * @param PDO $bd
 * @param string $nombre
 * @param string $password
 * @return object|null
 */

function recuperaUsuarioPorNombrePassword(PDO $bd, string $nombre, string $password): ?object {
    $consultaUsuario = 'select * from users where name=:name and password=:password';
    $stmtConsultaUsuario = $bd->prepare($consultaUsuario);
    $stmtConsultaUsuario->execute([":name" => $nombre, ":password" => $password]);
    $stmtConsultaUsuario->setFetchMode(PDO::FETCH_OBJ);
    if ($stmtConsultaUsuario->rowCount()) {
        $usuarioObject = $stmtConsultaUsuario->fetch();
    }
    return ($usuarioObject ?? null);
}

/**
 * Elimina un usuario de la BD
 * 
 * @param PDO $bd
 * @param string $usuarioId
 * @return bool
 */

function borraUsuarioPorId(PDO $bd, string $usuarioId): bool {
    $borraUsuario = "delete from users where id = :id";
    $stmtBorraUsuario = $bd->prepare($borraUsuario);
    $resultado = $stmtBorraUsuario->execute([":id" => $usuarioId]);
    return ($resultado);
}

/**
 * Inserta un usuario en la BD
 * 
 * @param PDO $bd
 * @param string $nombre
 * @param string $password
 * @param string $email
 * @return bool
 */

function insertaUsuario(PDO $bd, string $nombre, string $password, string $email = ""): bool {
    $insertaUsuario = "insert into users (name, password, email) values (:name, :password, :email)";
    $stmtInsertaUsuario = $bd->prepare($insertaUsuario);
    $result = $stmtInsertaUsuario->execute([":name" => $nombre, ":password" => $password, ":email" => $email]);
    return $result;
}

<?php

/*!
 * Jollof Framework (c) 2016
 *
 * {class_maps.php}
 *
 */

$compDir = basename(__DIR__);
$compDir = "/" .  $compDir;


return array(

     /* Core */

     "\\Providers\\Core\\App" => $compDir . "/providers/Core/App",
	"\\Providers\\Core\\HTTPResolver" => $compDir . "/providers/Core/HTTPResolver",
     "\\Providers\\Core\\HttpServer" => $compDir . "/providers/Core/HttpServer",
     "\\Providers\\Core\\HttpClient" => $compDir . "/providers/Core/HttpClient",
     "\\Providers\\Core\\SessionManager" => $compDir . "/providers/Core/SessionManager",
     "\\Providers\\Core\\InputManager" => $compDir . "/providers/Core/InputManager",
     "\\Providers\\Core\\QueryExtender" => $compDir . "/providers/Core/QueryExtender",
     "\\Providers\\Core\\QueryBuilder" => $compDir . "/providers/Core/QueryBuilder",

     "\\Providers\\Core\\DBConnection\\BaseConnectionAdapter" => $compDir . "/providers/Core/DBConnection/BaseConnectionAdapter",
     "\\Providers\\Core\\DBConnection\\MySqlConnectionAdapter" => $compDir . "/providers/Core/DBConnection/MySqlConnectionAdapter",
     "\\Providers\\Core\\DBConnection\\MsSqlConnectionAdapter" => $compDir . "/providers/Core/DBConnection/MsSqlConnectionAdapter",
     "\\Providers\\Core\\DBConnection\\PgSqlConnectionAdapter" => $compDir . "/providers/Core/DBConnection/PgSqlConnectionAdapter",
     "\\Providers\\Core\\DBConnection\\SqlLiteConnectionAdapter" => $compDir . "/providers/Core/DBConnection/SqlLiteConnectionAdapter",
     "\\Providers\\Core\\DBConnection\\MongoConnectionAdapter" => $compDir . "/providers/Core/DBConnection/MongoConnectionAdapter",

     /* Services */

     "\\Providers\\Services\\DBService" => $compDir . "/providers/Services/DBService",
     "\\Providers\\Services\\EnvService" => $compDir . "/providers/Services/EnvService",
	"\\Providers\\Services\\NativeSessionService" => $compDir . "/providers/Services/NativeSessionService",
     "\\Providers\\Services\\RedisSessionSerivce" => $compDir . "/providers/Services/RedisSessionSerivce",
	"\\Providers\\Services\\CacheService" => $compDir . "/providers/Services/CacheService",

     /* Tools */

     "\\Providers\\Tools\\ArgvInput" => $compDir . "/providers/Tools/ArgvInput",
     "\\Providers\\Tools\\Encrypter" => $compDir . "/providers/Tools/Encrypter",
     "\\Providers\\Tools\\Hasher" => $compDir . "/providers/Tools/Hasher",
     "\\Providers\\Tools\\ArgvOutput" => $compDir . "/providers/Tools/ArgvOutput",
     "\\Providers\\Tools\\Console" => $compDir . "/providers/Tools/Console",
     "\\Providers\\Tools\\JollofSecureHeaders" => $compDir . "/providers/Tools/JollofSecureHeaders",
     "\\Providers\\Tools\\SchemaObject" => $compDir . "/providers/Tools/SchemaObject",
     "\\Providers\\Tools\\SecureHeaders" => $compDir . "/providers/Tools/SecureHeaders",
     "\\Providers\\Tools\\AuthContext" => $compDir . "/providers/Tools/AuthContext",
     "\\Providers\\Tools\\InputFilter" => $compDir . "/providers/Tools/InputFilter",
     "\\Providers\\Tools\\RedisStorage" => $compDir . "/providers/Tools/RedisStorage",
     "\\Providers\\Tools\\MemcachedStorage" => $compDir . "/providers/Tools/MemcachedStorage",
     "\\Providers\\Tools\\TCPSocket" => $compDir . "/providers/Tools/TCPSocket",
     "\\Providers\\Tools\\SocketConnection" => $compDir . "/providers/Tools/SocketConnection",
     "\\Providers\\Tools\\LoginThrottle" => $compDir . "/providers/Tools/LoginThrottle",
     "\\Providers\\Tools\\TemplateRunner" => $compDir . "/providers/Tools/TemplateRunner",

     /* Policies */

     "\\Contracts\\Policies\\CacheAccessInterface" => $compDir . "/contracts/policies/Cache/CacheAccessInterface",
     "\\Contracts\\Policies\\DBAccessInterface" => $compDir . "/contracts/policies/DB/DBAccessInterface",
     "\\Contracts\\Policies\\SessionAccessInterface" => $compDir . "/contracts/policies/Session/SessionAccessInterface",

     /* Components */

     "\\Router" => $compDir . "/components/Router",
	"\\System" => $compDir . "/components/System",
     "\\Session" => $compDir . "/components/Session",
     "\\Auth" => $compDir . "/components/Auth",
     "\\File" => $compDir . "/components/File",
     "\\Request" => $compDir . "/components/Request",
     "\\Validator" => $compDir . "/components/Validator",
     "\\Response" => $compDir . "/components/Response",
     "\\Logger" => $compDir . "/components/Logger",
     "\\Comms" => $compDir . "/components/Comms",
     "\\Config" => $compDir . "/components/Config",
     "\\Cache" => $compDir . "/components/Cache",
     "\\TextStream" => $compDir . "/components/TextStream",
     "\\Helpers" => $compDir . "/components/Helpers",

     /* Models */

     "\\Model" => $compDir . "/../../models/Model",
     "\\User" => $compDir . "/../../models/User",
     "\\UserRole" => $compDir . "/../../models/UserRole",
     "\\UserThrottle" => $compDir . "/../../models/UserThrottle",
     "\\Todo" => $compDir . "/../../models/Todo",
     "\\TodoList" => $compDir . "/../../models/TodoList",

     /* Controllers */

     "\\Controller" => $compDir . "/../../controllers/Controller",
     "\\Chats" => $compDir . "/../../controllers/Chats",
     "\\Admin" => $compDir . "/../../controllers/Admin",
     "\\Account" => $compDir . "/../../controllers/Account",
     "\\Webhook" => $compDir . "/../../controllers/Webhook"


);


?>

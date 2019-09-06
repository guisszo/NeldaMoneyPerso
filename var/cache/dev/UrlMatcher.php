<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/depot' => [[['_route' => 'depot', '_controller' => 'App\\Controller\\DepotController::index'], null, null, null, false, false, null]],
        '/admin_user' => [[['_route' => 'admin_user_reg', '_controller' => 'App\\Controller\\PartenaireController::reguser'], null, ['POST' => 0], null, false, false, null]],
        '/contrat' => [[['_route' => 'contrat', '_controller' => 'App\\Controller\\PartenaireController::contrat'], null, ['GET' => 0], null, false, false, null]],
        '/register' => [[['_route' => 'app_register', '_controller' => 'App\\Controller\\RegistrationController::register'], null, null, null, false, false, null]],
        '/api/regpart' => [[['_route' => 'registerpartenaire', '_controller' => 'App\\Controller\\SuperAdminController::register'], null, ['POST' => 0], null, false, false, null]],
        '/api/registeruser' => [[['_route' => 'registeruser', '_controller' => 'App\\Controller\\SuperAdminController::reguser'], null, ['POST' => 0], null, false, false, null]],
        '/api/createCpt' => [[['_route' => 'crationCompte', '_controller' => 'App\\Controller\\SuperAdminController::creationCompte'], null, ['POST' => 0], null, false, false, null]],
        '/api/listePartblock' => [[['_route' => 'listePartblock', '_controller' => 'App\\Controller\\SuperAdminController::listePartblock'], null, ['GET' => 0], null, false, false, null]],
        '/api/listePart' => [[['_route' => 'listePart', '_controller' => 'App\\Controller\\SuperAdminController::listePart'], null, ['GET' => 0], null, false, false, null]],
        '/api/listeusers' => [[['_route' => 'listeusers', '_controller' => 'App\\Controller\\SuperAdminController::listerusers'], null, ['GET' => 0], null, false, false, null]],
        '/api/Partusers' => [[['_route' => 'PartUsers', '_controller' => 'App\\Controller\\SuperAdminController::PartUtil'], null, ['GET' => 0], null, false, false, null]],
        '/api/listePartenaires' => [[['_route' => 'listePartenaires', '_controller' => 'App\\Controller\\SuperAdminController::listePartenaires'], null, ['GET' => 0], null, false, false, null]],
        '/api/selecProfile' => [[['_route' => 'selecProfile', '_controller' => 'App\\Controller\\SuperAdminController::selectProfile'], null, ['GET' => 0], null, false, false, null]],
        '/api/selectCompte' => [[['_route' => 'selectCompte', '_controller' => 'App\\Controller\\SuperAdminController::selectCompte'], null, ['GET' => 0], null, false, false, null]],
        '/api/findNinea' => [[['_route' => 'findNinea', '_controller' => 'App\\Controller\\SuperAdminController::findNinea'], null, ['POST' => 0], null, false, false, null]],
        '/api/envoi' => [[['_route' => 'envoi', '_controller' => 'App\\Controller\\TransactionController::envoi'], null, ['POST' => 0], null, false, false, null]],
        '/api/retrait' => [[['_route' => 'retrait', '_controller' => 'App\\Controller\\TransactionController::retrait'], null, ['POST' => 0], null, false, false, null]],
        '/user' => [[['_route' => 'user', '_controller' => 'App\\Controller\\UserController::index'], null, null, null, false, false, null]],
        '/api/login' => [[['_route' => 'login', '_controller' => 'App\\Controller\\UtilisateurController::token'], null, ['POST' => 0], null, false, false, null]],
        '/api/depot' => [[['_route' => 'add_depot', '_controller' => 'App\\Controller\\UtilisateurController::Depot'], null, ['POST' => 0], null, false, false, null]],
        '/api/findCompte' => [[['_route' => 'findCompte', '_controller' => 'App\\Controller\\UtilisateurController::getCompt'], null, ['POST' => 0], null, false, false, null]],
        '/api/login_check' => [[['_route' => 'api_login_check'], null, null, null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/api(?'
                    .'|/modif_(?'
                        .'|user/([^/]++)(*:72)'
                        .'|partuser/([^/]++)(*:96)'
                    .')'
                    .'|(?:/(index)(?:\\.([^/]++))?)?(*:132)'
                    .'|/(?'
                        .'|d(?'
                            .'|ocs(?:\\.([^/]++))?(*:166)'
                            .'|epots(?'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:200)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:238)'
                                .')'
                            .')'
                        .')'
                        .'|co(?'
                            .'|ntexts/(.+)(?:\\.([^/]++))?(*:280)'
                            .'|mptes(?'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:314)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:352)'
                                .')'
                            .')'
                        .')'
                        .'|partenaires(?'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:395)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:433)'
                            .')'
                        .')'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_twig_error_test', '_controller' => 'twig.controller.preview_error::previewErrorPageAction', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        72 => [[['_route' => 'modif_user', '_controller' => 'App\\Controller\\SuperAdminController::update'], ['id'], ['PUT' => 0], null, false, true, null]],
        96 => [[['_route' => 'modif_partuser', '_controller' => 'App\\Controller\\SuperAdminController::updatePartuser'], ['id'], ['PUT' => 0], null, false, true, null]],
        132 => [[['_route' => 'api_entrypoint', '_controller' => 'api_platform.action.entrypoint', '_format' => '', '_api_respond' => 'true', 'index' => 'index'], ['index', '_format'], null, null, false, true, null]],
        166 => [[['_route' => 'api_doc', '_controller' => 'api_platform.action.documentation', '_format' => '', '_api_respond' => 'true'], ['_format'], null, null, false, true, null]],
        200 => [
            [['_route' => 'api_depots_get_collection', '_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_collection_operation_name' => 'get'], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_depots_post_collection', '_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_collection_operation_name' => 'post'], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        238 => [
            [['_route' => 'api_depots_get_item', '_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_item_operation_name' => 'get'], ['id', '_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_depots_delete_item', '_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_item_operation_name' => 'delete'], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'api_depots_put_item', '_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_item_operation_name' => 'put'], ['id', '_format'], ['PUT' => 0], null, false, true, null],
        ],
        280 => [[['_route' => 'api_jsonld_context', '_controller' => 'api_platform.jsonld.action.context', '_format' => 'jsonld', '_api_respond' => 'true'], ['shortName', '_format'], null, null, false, true, null]],
        314 => [
            [['_route' => 'api_comptes_get_collection', '_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_collection_operation_name' => 'get'], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_comptes_post_collection', '_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_collection_operation_name' => 'post'], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        352 => [
            [['_route' => 'api_comptes_get_item', '_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_item_operation_name' => 'get'], ['id', '_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_comptes_delete_item', '_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_item_operation_name' => 'delete'], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'api_comptes_put_item', '_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_item_operation_name' => 'put'], ['id', '_format'], ['PUT' => 0], null, false, true, null],
        ],
        395 => [
            [['_route' => 'api_partenaires_get_collection', '_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Partenaire', '_api_collection_operation_name' => 'get'], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_partenaires_post_collection', '_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Partenaire', '_api_collection_operation_name' => 'post'], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        433 => [
            [['_route' => 'api_partenaires_get_item', '_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Partenaire', '_api_item_operation_name' => 'get'], ['id', '_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_partenaires_delete_item', '_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Partenaire', '_api_item_operation_name' => 'delete'], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'api_partenaires_put_item', '_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Partenaire', '_api_item_operation_name' => 'put'], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];

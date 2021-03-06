<?php

// This file has been auto-generated by the Symfony Routing Component.

return [
    '_twig_error_test' => [['code', '_format'], ['_controller' => 'twig.controller.preview_error::previewErrorPageAction', '_format' => 'html'], ['code' => '\\d+'], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', '\\d+', 'code', true], ['text', '/_error']], [], []],
    'depot' => [[], ['_controller' => 'App\\Controller\\DepotController::index'], [], [['text', '/depot']], [], []],
    'admin_user_reg' => [[], ['_controller' => 'App\\Controller\\PartenaireController::reguser'], [], [['text', '/admin_user']], [], []],
    'contrat' => [[], ['_controller' => 'App\\Controller\\PartenaireController::contrat'], [], [['text', '/contrat']], [], []],
    'app_register' => [[], ['_controller' => 'App\\Controller\\RegistrationController::register'], [], [['text', '/register']], [], []],
    'registerpartenaire' => [[], ['_controller' => 'App\\Controller\\SuperAdminController::register'], [], [['text', '/api/regpart']], [], []],
    'registeruser' => [[], ['_controller' => 'App\\Controller\\SuperAdminController::reguser'], [], [['text', '/api/registeruser']], [], []],
    'modif_user' => [['id'], ['_controller' => 'App\\Controller\\SuperAdminController::update'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/modif_user']], [], []],
    'modif_partuser' => [['id'], ['_controller' => 'App\\Controller\\SuperAdminController::updatePartuser'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/modif_partuser']], [], []],
    'crationCompte' => [[], ['_controller' => 'App\\Controller\\SuperAdminController::creationCompte'], [], [['text', '/api/createCpt']], [], []],
    'listePartblock' => [[], ['_controller' => 'App\\Controller\\SuperAdminController::listePartblock'], [], [['text', '/api/listePartblock']], [], []],
    'listePart' => [[], ['_controller' => 'App\\Controller\\SuperAdminController::listePart'], [], [['text', '/api/listePart']], [], []],
    'listeusers' => [[], ['_controller' => 'App\\Controller\\SuperAdminController::listerusers'], [], [['text', '/api/listeusers']], [], []],
    'PartUsers' => [[], ['_controller' => 'App\\Controller\\SuperAdminController::PartUtil'], [], [['text', '/api/Partusers']], [], []],
    'listePartenaires' => [[], ['_controller' => 'App\\Controller\\SuperAdminController::listePartenaires'], [], [['text', '/api/listePartenaires']], [], []],
    'selecProfile' => [[], ['_controller' => 'App\\Controller\\SuperAdminController::selectProfile'], [], [['text', '/api/selecProfile']], [], []],
    'selectCompte' => [[], ['_controller' => 'App\\Controller\\SuperAdminController::selectCompte'], [], [['text', '/api/selectCompte']], [], []],
    'findNinea' => [[], ['_controller' => 'App\\Controller\\SuperAdminController::findNinea'], [], [['text', '/api/findNinea']], [], []],
    'onepart' => [['id'], ['id' => null, '_controller' => 'App\\Controller\\SuperAdminController::onepart'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/onepart']], [], []],
    'envoi' => [[], ['_controller' => 'App\\Controller\\TransactionController::envoi'], [], [['text', '/api/envoi']], [], []],
    'retrait' => [[], ['_controller' => 'App\\Controller\\TransactionController::retrait'], [], [['text', '/api/retrait']], [], []],
    'findCode' => [[], ['_controller' => 'App\\Controller\\TransactionController::findCode'], [], [['text', '/api/findCode']], [], []],
    'Trouvertarif' => [[], ['_controller' => 'App\\Controller\\TransactionController::trouverTarif'], [], [['text', '/api/Trouvertarif']], [], []],
    'Transactionsenv' => [[], ['_controller' => 'App\\Controller\\TransactionController::TransactListEnv'], [], [['text', '/api/listeTransactionsEnv']], [], []],
    'Transactionsretrait' => [[], ['_controller' => 'App\\Controller\\TransactionController::TransactListRetrait'], [], [['text', '/api/listeTransactionsRetrait']], [], []],
    'RechercheEnvoi' => [[], ['_controller' => 'App\\Controller\\TransactionController::RechercheEnv'], [], [['text', '/api/RechercheDateEnv']], [], []],
    'RechercheRetrait' => [[], ['_controller' => 'App\\Controller\\TransactionController::RechercheRetrait'], [], [['text', '/api/RechercheDateRetrait']], [], []],
    'user' => [[], ['_controller' => 'App\\Controller\\UserController::index'], [], [['text', '/user']], [], []],
    'login' => [[], ['_controller' => 'App\\Controller\\UtilisateurController::token'], [], [['text', '/api/login']], [], []],
    'add_depot' => [[], ['_controller' => 'App\\Controller\\UtilisateurController::Depot'], [], [['text', '/api/depot']], [], []],
    'findCompte' => [[], ['_controller' => 'App\\Controller\\UtilisateurController::getCompt'], [], [['text', '/api/findCompte']], [], []],
    'api_entrypoint' => [['index', '_format'], ['_controller' => 'api_platform.action.entrypoint', '_format' => '', '_api_respond' => 'true', 'index' => 'index'], ['index' => 'index'], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', 'index', 'index', true], ['text', '/api']], [], []],
    'api_doc' => [['_format'], ['_controller' => 'api_platform.action.documentation', '_format' => '', '_api_respond' => 'true'], [], [['variable', '.', '[^/]++', '_format', true], ['text', '/api/docs']], [], []],
    'api_jsonld_context' => [['shortName', '_format'], ['_controller' => 'api_platform.jsonld.action.context', '_format' => 'jsonld', '_api_respond' => 'true'], ['shortName' => '.+'], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', '.+', 'shortName', true], ['text', '/api/contexts']], [], []],
    'api_comptes_get_collection' => [['_format'], ['_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_collection_operation_name' => 'get'], [], [['variable', '.', '[^/]++', '_format', true], ['text', '/api/comptes']], [], []],
    'api_comptes_post_collection' => [['_format'], ['_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_collection_operation_name' => 'post'], [], [['variable', '.', '[^/]++', '_format', true], ['text', '/api/comptes']], [], []],
    'api_comptes_get_item' => [['id', '_format'], ['_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_item_operation_name' => 'get'], [], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', '[^/\\.]++', 'id', true], ['text', '/api/comptes']], [], []],
    'api_comptes_delete_item' => [['id', '_format'], ['_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_item_operation_name' => 'delete'], [], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', '[^/\\.]++', 'id', true], ['text', '/api/comptes']], [], []],
    'api_comptes_put_item' => [['id', '_format'], ['_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_item_operation_name' => 'put'], [], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', '[^/\\.]++', 'id', true], ['text', '/api/comptes']], [], []],
    'api_depots_get_collection' => [['_format'], ['_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_collection_operation_name' => 'get'], [], [['variable', '.', '[^/]++', '_format', true], ['text', '/api/depots']], [], []],
    'api_depots_post_collection' => [['_format'], ['_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_collection_operation_name' => 'post'], [], [['variable', '.', '[^/]++', '_format', true], ['text', '/api/depots']], [], []],
    'api_depots_get_item' => [['id', '_format'], ['_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_item_operation_name' => 'get'], [], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', '[^/\\.]++', 'id', true], ['text', '/api/depots']], [], []],
    'api_depots_delete_item' => [['id', '_format'], ['_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_item_operation_name' => 'delete'], [], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', '[^/\\.]++', 'id', true], ['text', '/api/depots']], [], []],
    'api_depots_put_item' => [['id', '_format'], ['_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_item_operation_name' => 'put'], [], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', '[^/\\.]++', 'id', true], ['text', '/api/depots']], [], []],
    'api_partenaires_get_collection' => [['_format'], ['_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Partenaire', '_api_collection_operation_name' => 'get'], [], [['variable', '.', '[^/]++', '_format', true], ['text', '/api/partenaires']], [], []],
    'api_partenaires_post_collection' => [['_format'], ['_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Partenaire', '_api_collection_operation_name' => 'post'], [], [['variable', '.', '[^/]++', '_format', true], ['text', '/api/partenaires']], [], []],
    'api_partenaires_get_item' => [['id', '_format'], ['_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Partenaire', '_api_item_operation_name' => 'get'], [], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', '[^/\\.]++', 'id', true], ['text', '/api/partenaires']], [], []],
    'api_partenaires_delete_item' => [['id', '_format'], ['_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Partenaire', '_api_item_operation_name' => 'delete'], [], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', '[^/\\.]++', 'id', true], ['text', '/api/partenaires']], [], []],
    'api_partenaires_put_item' => [['id', '_format'], ['_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Partenaire', '_api_item_operation_name' => 'put'], [], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', '[^/\\.]++', 'id', true], ['text', '/api/partenaires']], [], []],
    'api_login_check' => [[], [], [], [['text', '/api/login_check']], [], []],
];

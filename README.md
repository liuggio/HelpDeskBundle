HelpDeskTicketSystemBundle
==========================

Develpment status. Release will be on july.

The objective of this bundle is support the Help desk.

Open source support ticket system. (Symfony2 bundle)


See wiki

https://github.com/liuggio/HelpDeskTicketSystemBundle/wiki/Concept-key-RFC



Install this bundle as always :)


## INSTALLATION

1 Add the following entry to ``deps`` the run ``php bin/vendors install``.

``` yaml
 [HelpDeskTicketSystemBundle.git]
     git=https://github.com/liuggio/HelpDeskTicketSystemBundle.git
     target=/bundles/Liuggio/HelpDeskTicketSystemBundle
```

2 run bin/vendors install

3 Register the bundle in ``app/AppKernel.php``

``` php
    $bundles = array(
        // ...
        new Liuggio\HelpDeskTicketSystemBundle\LiuggioHelpDeskTicketSystemBundle(),
    );
```

4  Register namespace in ``app/autoload.php``

``` php
    $loader->registerNamespaces(array(
         // ...
         'Liuggio'           =>  __DIR__.'/../vendor/bundles',
     ));
```


5  Add to app/config/routing.yml

 ``` yaml

 LiuggioHelpDeskTicketSystemBundle_customer_care_ticket:
     resource: "@LiuggioHelpDeskTicketSystemBundle/Resources/config/routing.yml"
     prefix:   /

 ```


6 Add the following entries to security.yml

    access_control:
        //...
        # HelpDesk Ticket system
        - { path: ^/customer-care/operator, role: [ROLE_CUSTOMERCARE, ROLE_SONATA_ADMIN, ROLE_ADMIN,] }
        - { path: ^/customer-care/, role: [IS_AUTHENTICATED_FULLY]}DMIN,] }


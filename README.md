HelpDeskBundle
==========================

Develpment status. Release will be on july.


** This bundle is not working yet!!! **


The objective of this bundle is support the Help desk.

Open source support ticket system. (Symfony2 bundle)


See wiki

https://github.com/liuggio/HelpDeskBundle/wiki/Concept-key-RFC



Install this bundle as always :)


## Composer: add to your composer

1 Add the following entry to ``deps`` the run ``php bin/vendors install``.

``` json

  'liuggio/help-desk-bundle':"dev-master"

```

2 Register the bundle in ``app/AppKernel.php``

``` php
    $bundles = array(
        // ...
        new Liuggio\HelpDeskBundle\LiuggioHelpDeskBundle(),
    );
```


3  Add to app/config/routing.yml

 ``` yaml

 LiuggioHelpDeskBundle_customer_care_ticket:
     resource: "@LiuggioHelpDeskBundle/Resources/config/routing.yml"
     prefix:   /help-desk

 ```

or

 ``` yaml

myLiuggioHelpDeskBundle_customer_care_ticket:
    resource: "@LiuggioHelpDeskBundle/Resources/config/routing/user.yml"
    prefix:   /help-desk/my

myLiuggioHelpDeskBundle_customer_care_operator_ticket:
    resource: "@LiuggioHelpDeskBundle/Resources/config/routing/operator.yml"
    prefix:   /help-desk/operator


```


4 Add the following entries to config.yml
``` yaml

liuggio_help_desk:
    class:
        ticket: Liuggio\HelpDeskBundle\Entity\Ticket      #optional
        comment: Liuggio\HelpDeskBundle\Entity\Comment    #optional
        category: Liuggio\HelpDeskBundle\Entity\Category  #optional
        user:     YOUR/NAMESPACE/ENTITY/CLASS
    email:
        sender: terravision-developers@googlegroups.com
        subject_prefix: '[help Desk]'                     #optional

```


5 Add the following entries to security.yml

    access_control:
        //...
        # HelpDesk Ticket system
        - { path: ^/customer-care/operator, role: [ROLE_CUSTOMERCARE, ROLE_SONATA_ADMIN, ROLE_ADMIN,] }
        - { path: ^/customer-care/, role: [IS_AUTHENTICATED_FULLY]}DMIN,] }


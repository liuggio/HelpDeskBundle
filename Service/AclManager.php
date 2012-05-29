<?php
namespace Liuggio\HelpDeskTicketSystemBundle\Service;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use FOS\UserBundle\Entity\User;

/**
 * Created by JetBrains PhpStorm.
 * User: toretto460
 * Date: 4/5/12
 * Time: 10:59 AM
 * To change this template use File | Settings | File Templates.
 */
class AclManager
{

    private $aclProvider;
    private $securityContext;

    public function __construct($aclProvider, $securityContext)
    {
        $this->aclProvider = $aclProvider;
        $this->securityContext = $securityContext;

    }

    /**
     * @param $object
     * @return mixed
     */
    public function getOrCreateAcl($object)
    {
        $objectIdentity = ObjectIdentity::fromDomainObject($object);

        try{
            $acl = $this->aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e){
            $acl = $this->aclProvider->createAcl($objectIdentity);
        }
        return $acl;

    }

    /**
     * @param $object
     * @param $user
     * @param int $permissions
     */
    public function insertAce($object, User $user, $permissions = MaskBuilder::MASK_OWNER )
    {
        $acl = $this->getOrCreateAcl($object);
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        // setting Owner
        $acl->insertObjectAce($securityIdentity,$permissions);
        $this->aclProvider->updateAcl($acl);

    }

    public function checkPermissions($object, $permissions = 'OWNER')
    {
        // verifica per l'accesso in modifica
        if (false === $this->securityContext->isGranted($permissions, $object))
        {
            throw new AccessDeniedException(); 
        }

        return true;

    }


}

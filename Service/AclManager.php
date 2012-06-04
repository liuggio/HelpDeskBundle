<?php
namespace Liuggio\HelpDeskTicketSystemBundle\Service;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Tvision\Bundle\UserBundle\Entity\User;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Created by JetBrains PhpStorm.
 * User: toretto460
 * Date: 4/5/12
 * Time: 10:59 AM
 * To change this template use File | Settings | File Templates.
 */
class AclManager implements ContainerAwareInterface
{

    private $aclProvider;
    private $securityContext;
    private $container;

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
    public function insertAce($ticket, User $user, $permissions = MaskBuilder::MASK_OWNER )
    {
        $acl = $this->getOrCreateAcl($ticket);
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        // setting Owner
        $acl->insertObjectAce($securityIdentity,$permissions);
        $this->aclProvider->updateAcl($acl);

    }

    public function checkOpPermissions($ticket, $user, $qb)
    {
        $qb->select('t')
            ->from('LiuggioHelpDeskTicketSystemBundle:Ticket', 't')
            ->leftjoin('t.category', 'ct')
            ->leftjoin('ct.operators','opr')
            ->where('t = :ticket')
            ->andWhere('opr = :user')
            ->setParameter('ticket', $ticket)
            ->setParameter('user', $user);

        $result = $qb->getQuery()->getResult();

            if(empty($result)) {
                throw new AccessDeniedException("Category Permission not granted!");
            }

        return true;
    }

    public function checkPermissions($ticket, $permissions = 'OWNER')
    {
        if(false === $this->securityContext->isGranted($permissions, $ticket)) {
            throw new AccessDeniedException();
        }

        return true;
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}

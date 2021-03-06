<?php

namespace Repository;

use Doctrine\ORM\EntityRepository;


class UserRepository extends EntityRepository
{
	
	public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class)
	{
		parent::__construct($em, $class);
	}
	
	public function findFriendsOf(\Entity\User $user)
	{
		$query = $this->createQueryBuilder("u")
				->innerJoin("u.relationshipFrom","rel_to")
				->innerJoin("rel_to.to", "friend")
				->innerJoin("friend.relationshipFrom", "rel_back")
				->where("rel_to.type = " . \Entity\UserRelationship::TYPE_FRIEND)
				->andwhere("rel_back.type = " . \Entity\UserRelationship::TYPE_FRIEND)
				->andwhere("rel_back.to = u.id")
				->andwhere("friend.id = " . $user->getId())
				->getQuery();

	    return $query->getResult();
	}
	
	public function findFriendshipInvitationsOf(\Entity\User $user)
	{
		$query = $this->createQueryBuilder("u")
				->innerJoin("u.relationshipFrom","rel_to")
				->innerJoin("rel_to.to", "friend")
				->leftJoin("friend.relationshipFrom", "rel_back", \Doctrine\ORM\Query\Expr\Join::WITH, 'rel_back.to = rel_to.from' )
				->where("rel_to.type = " . (\Entity\UserRelationship::TYPE_FRIEND))
				->andwhere("rel_back.to IS NULL")
				->andwhere("friend.id = " . $user->getId())
				->getQuery();
				
	    return $query->getResult();
	}
	
	
	
	public function findMembershipRequestsOf(\Entity\User $user)
	{
		$query = $this->_em->getRepository("Entity\UserGroup")->createQueryBuilder("ug")
					->innerJoin("ug.group", "g")
					->innerJoin("g.userGroups", "manager")
					->where("manager.user = " . $user->getId())
					->andwhere("manager.role = " . (\Entity\UserGroup::ROLE_MANAGER))
					->andwhere("ug.requestedRole = 10")
					->getQuery();
					
		return $query->getResult();
	}
	
	public function findMembershipInvitations(\Entity\User $user)
	{
		$query = $this->_em->getRepository("Entity\UserGroup")->createQueryBuilder("ug")
					->where("ug.user = " . $user->getId())
					->andWhere("ug.invitationAccepted = FALSE")
					->getQuery();
		
		return $query->getResult();
	}
	
}

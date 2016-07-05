# Examples

Here is some real life examples:

```php
<?php

use Thruster\Bundle\ViewsBundle\View\View;
use AppBundle\Entity\User;

class UserView extends View
{
	public function afterRegistration(User $user)
	{
		return [
			'id' => $user->getId(),
			'username' => $user->getUsername(),
			'email' => $user->getEmail()
		];
	},
	
	public function publicUser(User $user)
	{
		return [
			'id' => $user->getId(),
			'username' => $user->getUsername(),
			'profile_picture' => $user->getProfilePicture()
		];
	}
	
	public function fullPublicUser($data)
	{
		$user = $data['user'];
	
		return [
			'id' => $user->getId(),
			'username' => $user->getUsername(),
			'profile_picture' => $user->getProfilePicture(),
			'bio' => $user->getBio(),
			'website' => $user->getWebsite(),
			'count' => [
				'following' => $data['follows'],
				'followers' => $data['followers']
			]
		];
	}
}

class RelationshipView extends View
{
	public function follows($relationships)
	{
		return [
			'data' => $this->renderMany([$this, 'follow'], $relationships)
		];
	}
	
	public function follow($relationship)
	{
		return $this-renderOne('AppBundle:User:publicUser', $relationship->getUser());
	}
	
	public function followedBy($relationships)
	{
		return [
			'data' => $this->renderMany([$this, 'followBy'], $relationships)
		];
	}
	
	public function followBy($relationship)
	{
		return $this-renderOne('AppBundle:User:publicUser', $relationship->getUser());
	}
	
	public function status($data)
	{
		return [
			'data' => [
				'outgoing_status' => $this->outgoingStatus($data['outgoing']),
				'incoming_status' => $this->incomingStatus($data['incoming']),
			]
		];
	}
	
	public function outgoingStatus($status)
	{
		switch ($status) {
			case 1:
				return 'followed_by_you';
			case 2:
				return 'blocked_by_you'
			default:
				return 'none';
		}
	}
	
	public function incomingStatus($status)
	{
		switch ($status) {
			case 1:
				return 'follows';
			default:
				return 'none';
		}
	}
}

?>

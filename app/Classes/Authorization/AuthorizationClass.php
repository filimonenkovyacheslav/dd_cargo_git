<?php

namespace App\Classes\Authorization;

use Dlnsk\HierarchicalRBAC\Authorization;


/**
 *  This is example of hierarchical RBAC authorization configiration.
 */

class AuthorizationClass extends Authorization
{
	public function getPermissions() {
		return [
			'update-post' => [
                    'description' => 'Редактирование любых статей',
                ],
            'update-user' => [
                    'description' => 'Редактирование пользователей',
                ],   
			'editPost' => [
					'description' => 'Edit any posts',   // optional property
					'next' => 'editOwnPost',            // used for making chain (hierarchy) of permissions
				],
			'editColumns-1' => [
					'description' => 'Edit own columns',
				],
			'editColumns-2' => [
					'description' => 'Edit own columns',
				],
			'editColumns-3' => [
					'description' => 'Edit own columns',
				],
			'view-post' => [
					'description' => 'View post',
				],
			'china-view-post' => [
					'description' => 'View post',
				],
			'china-update-post' => [
					'description' => 'Update post',
				],
			'eng-view-post' => [
					'description' => 'View post',
				],
			'eng-update-post' => [
					'description' => 'Update post',
				],
			'editColumns-eng' => [
					'description' => 'Edit own columns',
				],
			'editColumns-eng-2' => [
					'description' => 'Edit own columns',
				],
			'editOwnPost' => [
					'description' => 'Edit own post',
				]			
		];
	}

	public function getRoles() {
		return [
			'warehouse' => [
					'view-post',
					'update-post',
					'editColumns-2',
					'editColumns-3',
					'editColumns-eng-2',
					'eng-view-post',
					'eng-update-post'
				],
			'office_1' => [
					'view-post',
					'update-post',
					'editPost',
					'editColumns-3',
					'china-update-post',
					'china-view-post',
					'eng-update-post',
					'editColumns-eng',
					'eng-view-post',
					'editColumns-1'
				],
			'office_2' => [
					'view-post',
					'update-post',
					'editColumns-1'					
				],
			'viewer' => [
					'view-post'
				],
			'viewer_1' => [
					'view-post'
				],
			'viewer_2' => [
					'view-post'
				],
			'viewer_3' => [
					'view-post'
				],
			'viewer_4' => [
					'view-post'
				],
			'viewer_5' => [
					'view-post'
				],
			'china_admin' => [
					'china-view-post',
					'china-update-post'
				],
			'china_viewer' => [
					'china-view-post',
				],
			'office_eng' => [
					'eng-view-post',
					'eng-update-post',
					'editColumns-eng'					
				],
			'viewer_eng' => [
					'eng-view-post',
				],			
			'user' => [
					'editOwnPost',
				],
		];
	}


	/**
	 * Methods which checking permissions.
	 * Methods should be present only if additional checking needs.
	 */

	public function editOwnPost($user, $post) {
		$post = $this->getModel(\App\Post::class, $post);  // helper method for geting model

		return $user->id === $post->user_id;
	}

}

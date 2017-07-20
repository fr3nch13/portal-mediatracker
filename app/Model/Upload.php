<?php
App::uses('AppModel', 'Model');

/**
 * Upload Model
 *
 * @property User $User
 * @property Category $Category
 * @property Report $Report
 */
class Upload extends AppModel 
{

	public $displayField = 'filename';
	
	public $validate = array(
		'filename' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'public' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'md5' => array(
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'This file already exists.',
			),
		),
		// checking that the file is uploaded, etc
		'file' => array(
			'exists' => array(
				'rule' => array('RuleFile'),
				'message' => 'There was an error with this file',
				'last'    => true,
				'allowEmpty' => true
			),
		),
	);
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'Media' => array(
			'className' => 'Media',
			'foreignKey' => 'media_id',
		),
		'CustodyChain' => array(
			'className' => 'CustodyChain',
			'foreignKey' => 'custody_chain_id',
		),
	);
	
	public $hasAndBelongsToMany = array(
		'Keyword' => array(
			'className' => 'Keyword',
			'joinTable' => 'uploads_keywords',
			'foreignKey' => 'upload_id',
			'associationForeignKey' => 'keyword_id',
			'unique' => 'keepExisting',
			'with' => 'UploadsKeyword',
		),
	);
	
	public $actsAs = array(
		'Dblogger.Dblogger', // log all changes to the database
	);
	
	
	// define the fields that can be searched
	public $searchFields = array(
		'Upload.filename',
		'Upload.mimetype',
		'Upload.type',
		'Upload.md5',
	);
	
	// set in beforeSave/beforeDelete and used in afterSave/afterDelete
	public $file_info = false;
	
	// set in beforeDelete and used in afterDelete
	public $delete_file = false;
	
	// holds a copy of the active Upload model
	public $Upload = false;
	
	// determine where to redirect after a save
	public $saveRedirect = false;
	
	
	// used to validate, and if there are errors, overwrite the file error
	public function validates($options = array())
	{
		// allow it to be empty
		if(isset($this->data['Upload']['error']) and $this->data['Upload']['error'] === 4)
		{
			return true;
		}
		// allow it to be empty
		if(isset($this->data['Upload']['file']['error']) and $this->data['Upload']['file']['error'] === 4)
		{
			return true;
		}
		
		$tmp_name = false;
		if(isset($this->data['Upload']['file']['tmp_name']))
		{
			$tmp_name = $this->data['Upload']['file']['tmp_name'];
		}
		else
		{
			return true;
		}
		
		// fill out the md5
		$this->data['Upload']['md5'] = md5_file($tmp_name);
		
		$valid = parent::validates($options);
		
		if(isset($this->validationErrors['md5']))
		{
			if(!isset($this->validationErrors['file']))
			{
				$this->validationErrors['file'] = array();
			}
			$this->validationErrors['file'] = array_merge($this->validationErrors['file'], $this->validationErrors['md5']);
		}
		
		return $valid;
	}
	
	public function beforeSave($options = array())
	{
		// see if the file uploaded ok
		// if there is no id, then it's being created
		if($this->data['Upload'] and !$this->id and !isset($this->data['Upload']['id']))
		{
			// make sure [file] exists
			if(isset($this->data['Upload']['file']))
			{
				$this->data['Upload'] = array_merge($this->data['Upload']['file'], $this->data['Upload']);
				unset($this->data['Upload']['file']);
			}
			
			if($this->data['Upload']['error'] != 0 or $this->data['Upload']['error'] == 4)
			{
				unset($this->data['Upload']);
				return parent::beforeSave();
			}
			
			// make sure the user is associated with this upload when adding a new one
			if(!isset($this->data['Upload']['user_id']))
			{
				$this->data['Upload']['user_id'] = AuthComponent::user('id');
			}
			
			// set some more variables based on the file info
			$this->data['Upload']['filename'] = $this->data['Upload']['name'];
			$this->data['Upload']['mimetype'] = $this->data['Upload']['type'];
			$this->data['Upload']['size'] = $this->data['Upload']['size'];
			$this->data['Upload']['type'] = pathinfo($this->data['Upload']['name'], PATHINFO_EXTENSION);
			$this->data['Upload']['md5'] = md5_file($this->data['Upload']['tmp_name']);
			
			// for some reason *.log files are coming in as application/octet-stream.
			// (see also dump files)
			if(strtolower($this->data['Upload']['type']) == 'log')
			{
				$this->data['Upload']['mimetype'] = 'text/plain';
			}
			
			if(isset($this->data['Upload']['media_id']) and $this->data['Upload']['media_id'] and isset($this->data['Upload']['new_media']))
			{
				$custody_chain = $this->Media->CustodyChain->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'CustodyChain.media_id' => $this->data['Upload']['media_id'],
					),
				));
				
				if($custody_chain)
				{
					$this->data['Upload']['custody_chain_id'] = $custody_chain['CustodyChain']['id'];
				}
			}
			
			$this->file_info = $this->data['Upload'];
//			unset($this->data['Upload']['file']);
		}
		return parent::beforeSave($options);
	}

	public function afterSave($created = false, $options = array())
	{
		// move the file to it's proper location
		$file_path = false;
		if($this->file_info)
		{
			$file_info = $this->file_info;
			$paths = $this->paths($this->data['Upload']['id'], true, $file_info['name']);
			if($paths['sys'])
			{
				umask(0);
				if(rename($file_info['tmp_name'], $paths['sys']))
				{
					$file_path = $paths['sys'];
				}
			}
		}
		
		if(isset($this->data['Upload']['id']) and $file_path)
		{
			// Save the vectors
			$vectors = array();
		
			$mimetype = false;
			if(isset($this->data['Upload']['mimetype']))
			{
				$mimetype = $this->data['Upload']['mimetype'];
			}
			
			// save the keywords
			$keywords = array();
			$all_keywords = $this->extractKeywords(false, $file_path);
			
			if($all_keywords)
			{
				foreach($all_keywords as $i => $keyword)
				{
					$keyword = trim($keyword);
					$keywords[$keyword] = $keyword; // format and make unique
				}
			}
			
			if($keywords)
			{
				sort($keywords);
				
				$data = array(
					'UploadsKeyword' => array(
						'keywords' => $keywords,
						'upload_id' => $this->id,
					),
				);
				
				$this->UploadsKeyword->add($data);
			}
		}
		
		return parent::afterSave($created, $options);
	}
	
	public function beforeDelete($cascade = true)
	{
		// find the info for deleting the file
		if($filename = $this->field('filename'))
		{
			$paths = $this->paths($this->id, false, $filename);
			$this->delete_file = $paths['sys'];
		}
		return parent::beforeDelete($cascade);
	}
	
	public function afterDelete()
	{
		// delete the file
		if($this->delete_file)
		{
			// try to delete the file
			if(is_file($this->delete_file) and is_writable($this->delete_file)) unlink($this->delete_file);
			
			// should be the only file in this directory
			$path_parts = explode(DS, $this->delete_file);
			// remove the filename
			array_pop($path_parts);
			while($path_parts)
			{
				$this_dir = array_pop($path_parts);
				if($this_dir == 'uploads') break;
				if(in_array($this_dir, range(0, 9)))
				{
					$dir = implode(DS, $path_parts). DS. $this_dir;
					$listing = glob ("$dir/*");
					// directory is empty
					if(empty($listing)) rmdir($dir);
				}
			}
		}
		
		return parent::afterDelete();
	}
	
	public function saveAssociated($data = false, $options = array())
	{
	/*
	 * Wrapper Used to unzip and save multiple objects from a zip file
	 * If the uploaded file is a zip
	 */
		// check if the upload file is a zip
		// if so, restructure the data array to include multiple Uploads
		if(
			isset($data['Upload']) 
			and isset($data['Upload']['file']) 
			and $data['Upload']['file']['error'] == 0
			and in_array($data['Upload']['file']['type'], $this->zipMimeTypes))
		{
			$zip_details = $data['Upload']['file'];
			$files = $this->processZipFile($zip_details['tmp_name'], true);
			unset($data['Upload']['file']);
			$_data = $data['Upload'];
			$data = array();
			
			// restructure the data array
			$i = 0;
			foreach($files as $file)
			{
				$file_name = array_pop(explode(DS, $file));
				
				// fill out the 'Upload' array
				$data[$i]['Upload'] = $_data;
				$data[$i]['Upload']['file'] = array(
					'name' => $file_name,
					'type' => mime_content_type($file),
					'tmp_name' => $file,
					'error' => 0,
					'size' => filesize($file),
				);
				$i++;
			}
		
			$this->set($data);
			
			if($return = parent::saveAll($data))
			{
				// remove the zip directory
				$this->removeZipDir();
			}
		}
		else
		{
			$return = parent::saveAssociated($data, $options);
		}
		
		return $return;
	}
	
	function paths($upload_id = false, $create = false, $filename = false)
	{
		$paths = array('web' => false, 'sys' => false);
		
		if(!$upload_id) return false;
		
		$paths['web'] = DS. 'files'. DS. 'uploads'. DS;
		$paths['sys'] = WWW_ROOT. ltrim($paths['web'], DS);
		
		foreach(str_split($upload_id) as $num)
		{
			$paths['sys'] .= $num. DS;
			$paths['web'] .= $num. DS;
		}
		
		if($create)
		{
			umask(0);
			if(!is_dir($paths['sys'])) mkdir($paths['sys'], 0777, true);
		}
		
		if($filename)
		{
			$paths['sys'] .= $filename;
			$paths['web'] .= $filename;
		}
		
		return $paths;
	}
	
	// Validation Rules
	public function RuleFile($file = false)
	{
		if(!$file) return false;
		if(!isset($file['file']['error'])) return false;
		if($file['file']['error'] === 0) return true;
		
		$max_upload = (int)(ini_get('upload_max_filesize'));
		$max_post = (int)(ini_get('post_max_size'));
		$memory_limit = (int)(ini_get('memory_limit'));
		$upload_mb = min($max_upload, $max_post, $memory_limit);
		
		// change the validation message based on what the file error is
		$errorMessage = '';
		switch ($file['file']['error'])
		{
			case 1: 
			case 2: 
				$errorMessage = __('The uploaded file exceeds allowed size of %sM', $upload_mb); 
				break;
			case 3: 
				$errorMessage = __('The uploaded file was only partially uploaded.'); 
				break;
			case 4: 
				$errorMessage = __('No file was uploaded.'); 
				break;
			case 6: 
				$errorMessage = __('Missing a temporary upload folder.'); 
				break;
			case 7: 
				$errorMessage = __('Failed to write file to disk.'); 
				break;
			default:
				$errorMessage = __('Error ID: %s', $file['file']['error']); 
				break;
				
			
		}
		$this->validationErrors['file'][] = $errorMessage;
		return $errorMessage;
	}
}
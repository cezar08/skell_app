<?php
/**
 * Módulo Admin
 *
 * @link      http://...
 * @copyright Copyright (c) 2016 Disciplina de Desenvolvimento com Frameworks
 * @license   Private
 */

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table (name = "admin_autenticacao")
 *
 * @author  Cezar Junior de Souza <cezar08@unochapeco.edu.br
 * @category Admin
 * @package Entity
 */
class Autenticacao
{
   	/**
 	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	* @ORM\Column(type="integer", name="id_autenticacao")
	*
	* @var int
	*/
	protected $id;
	
  	/**
	* @ORM\Column(type="string")
	*
	* @var string
	*/
	protected $desc_tipo_autenticacao;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getDescTipoAutenticacao()
	{
		return $this->desc_tipo_autenticacao;
	}

	/**
	 * @param string $desc_tipo_autenticacao
	 */
	public function setDescTipoAutenticacao($desc_tipo_autenticacao)
	{
		$this->desc_tipo_autenticacao = $desc_tipo_autenticacao;
	}
}

<?php
namespace Admin\Entity;

use Core\Test\ModelTestCase;
use \Admin\Entity\Autenticacao;

/**
 * @group Entity
 */
class AutenticacaoTest extends ModelTestCase
{

    public function testInsert()
    {
        $autenticacao = new Autenticacao();
		$autenticacao->setDescTipoAutenticacao('LDAP');
		$this->getEntityManager()->persist($autenticacao);
		$this->getEntityManager()->flush();
		$dados_autenticacao = $this->getEntityManager()
		->getRepository('\Admin\Entity\Autenticacao')->findAll();
		$this->assertEquals(1, count($dados_autenticacao));
		$this->assertEquals('LDAP', $dados_autenticacao[0]->getDescTipoAutenticacao());
		$this->assertEquals(1, $dados_autenticacao[0]->getId());
    }

	/**
	 * @expectedException \Doctrine\DBAL\Exception\NotNullConstraintViolationException
	 */
    public function testInsertInvalid()
    {
        $autenticacao = new Autenticacao();
		$autenticacao->setDescTipoAutenticacao(null);
		$this->getEntityManager()->persist($autenticacao);
		$this->getEntityManager()->flush();
    }

}


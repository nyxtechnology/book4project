<?php


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class ValidatePbfCest
{
  public function GroupPermissions (AcceptanceTester $I) {
    $I->comment('**Criando grupo**');
    $I->amOnPage('/user/login');
    $I->fillField('[id=edit-name]', 'admin');
    $I->fillField('[id=edit-pass]', '12345');
    $I->click('[id=edit-submit]');
    $I->wait(1);
    $I->see('Membro há');
    $I->amOnPage('/admin/content');
    $I->click('Add content');
    $I->click('Grupo');
    $I->fillField('[id=edit-title-0-value]', 'Usuario');
    $I->uncheckOption('input[name="status[value]"]');
    $I->click('.button.button--primary.js-form-submit.form-submit');

    $I->comment('**Adicionando usuario Supervisor ao grupo de usuarios**');
    $I->amOnPage('/admin/people');
    $I->click('Supervisor');
    $I->click('Edit', 'div[id=block-claro-primary-local-tasks]');
    $I->fillField('input[id=edit-field-pbf-group-0-target-id]', 'Usuario');
    $I->click('.button.button--primary.js-form-submit.form-submit');

    $I->comment('**Criando conteudo para os usuarios do grupo de usuarios');
    $I->amOnPage('/node/add');
    $I->click('Página básica');
    $I->fillField('input[name="title[0][value]"]', 'Diretor de Super Mario RPG não vai voltar para o remake');
    $I->fillField('input[name="field_pbf_group[2][target_id]"]', 'Usuario');
    $I->checkOption('input[name="field_pbf_group[2][grant_view]"]');
    $I->click('[id=edit-options]');
    $I->checkOption('input[id=edit-promote-value]');
    $I->click('.button.button--primary.js-form-submit.form-submit');
    $I->wait(1);
    $I->amOnPage('/user/logout');

    $I->comment('**Testando se usuario Supervisor consegue ver o conteudo**');
    $I->amOnPage('/user/login');
    $I->fillField('[id=edit-name]', 'Supervisor');
    $I->fillField('[id=edit-pass]', '12345');
    $I->click('[id=edit-submit]');
    $I->wait(1);
    $I->see('Membro há');
    $I->amOnPage('/');
    $I->see('Diretor de Super Mario RPG não vai voltar para o remake');
    $I->click('Diretor de Super Mario RPG não vai voltar para o remake');
    $I->dontSee('Edit', 'div[id=block-claro-primary-local-tasks]');

    $I->comment('**Testando se usuario Escritor fora do grupo consegue ver o conteudo**');
    $I->amOnPage('/user/logout');
    $I->amOnPage('/user/login');
    $I->fillField('[id=edit-name]', 'Escritor');
    $I->fillField('[id=edit-pass]', '12345');
    $I->click('[id=edit-submit]');
    $I->wait(1);
    $I->see('Membro há');
    $I->amOnPage('/');
    $I->dontSee('Diretor de Super Mario RPG não vai voltar para o remake');

    $I->comment('**Dando permissão de editar conteudo ao grupo de Usuario**');
    $I->amOnPage('/user/logout');
    $I->amOnPage('/user/login');
    $I->fillField('[id=edit-name]', 'admin');
    $I->fillField('[id=edit-pass]', '12345');
    $I->click('[id=edit-submit]');
    $I->see('Membro há');
    $I->amOnPage('/');
    $I->see('Diretor de Super Mario RPG não vai voltar para o remake');
    $I->click('Diretor de Super Mario RPG não vai voltar para o remake');
    $I->see('Edit');
    $I->click('Edit', 'div[id=block-claro-primary-local-tasks]');
    $I->checkOption('input[name="field_pbf_group[2][grant_view]"]');
    $I->checkOption('input[name="field_pbf_group[2][grant_update]"]');
    $I->click('.button.button--primary.js-form-submit.form-submit');
    $I->wait(1);
    $I->amOnPage('/user/logout');
    $I->amOnPage('/user/login');
    $I->fillField('[id=edit-name]', 'Supervisor');
    $I->fillField('[id=edit-pass]', '12345');
    $I->click('[id=edit-submit]');
    $I->wait(1);
    $I->amOnPage('/');
    $I->see('Diretor de Super Mario RPG não vai voltar para o remake');
    $I->click('Diretor de Super Mario RPG não vai voltar para o remake');
    $I->see('Edit');
    $I->click('Edit', 'div[id=block-claro-primary-local-tasks]');
    $I->wait(1);

    $I->comment('**Adicionando usuario Editor ao grupo de usuarios**');
    $I->amOnPage('/user/logout');
    $I->amOnPage('/user/login');
    $I->fillField('[id=edit-name]', 'admin');
    $I->fillField('[id=edit-pass]', '12345');
    $I->click('[id=edit-submit]');
    $I->see('Membro há');
    $I->amOnPage('/admin/people');
    $I->click('Escritor');
    $I->click('Edit', 'div[id=block-claro-primary-local-tasks]');
    $I->fillField('input[id=edit-field-pbf-group-0-target-id]', 'Usuario');
    $I->click('.button.button--primary.js-form-submit.form-submit');
    $I->wait(1);

    $I->comment('**Verificando se usuario Editor consegue ver e editar conteudo**');
    $I->amOnPage('/user/logout');
    $I->amOnPage('/user/login');
    $I->fillField('[id=edit-name]', 'Escritor');
    $I->fillField('[id=edit-pass]', '12345');
    $I->click('[id=edit-submit]');
    $I->see('Membro há');
    $I->amOnPage('/');
    $I->see('Diretor de Super Mario RPG não vai voltar para o remake');
    $I->click('Diretor de Super Mario RPG não vai voltar para o remake');
    $I->see('Edit');
    $I->click('Edit', 'div[id=block-claro-primary-local-tasks]');

    $I->comment('**Deletando conteudo e grupo**');
    $I->amOnPage('/user/logout');
    $I->amOnPage('/user/login');
    $I->fillField('[id=edit-name]', 'admin');
    $I->fillField('[id=edit-pass]', '12345');
    $I->click('[id=edit-submit]');
    $I->see('Membro há');
    $I->amOnPage('/');
    $I->see('Diretor de Super Mario RPG não vai voltar para o remake');
    $I->click('Diretor de Super Mario RPG não vai voltar para o remake');
    $I->see('Delete');
    $I->click('Delete', 'div[id=block-claro-primary-local-tasks]');
    $I->wait(1);
    $I->click('.button.button--primary.js-form-submit.form-submit');
    $I->amOnPage('/');
    $I->dontSee('Diretor de Super Mario RPG não vai voltar para o remake');
    $I->amOnPage('/admin/content');
    $I->see('Usuario');
    $I->click('Usuario');
    $I->see('Delete');
    $I->click('Delete', 'div[id=block-claro-primary-local-tasks]');
    $I->click('.button.button--primary.js-form-submit.form-submit');
    $I->amOnPage('/admin/content');
    $I->dontSee('Usuario');
  }
}

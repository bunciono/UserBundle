<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use FOS\UserBundle\DependencyInjection\FOSUserExtension;
use Symfony\Component\Yaml\Parser;

class FOSUserExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $configuration;

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessDatabaseDriverSet()
    {
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['db_driver']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUserLoadThrowsExceptionUnlessDatabaseDriverIsValid()
    {
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['db_driver'] = 'foo';
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessFirewallNameSet()
    {
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['firewall_name']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessGroupModelClassSet()
    {
        $loader = new FOSUserExtension();
        $config = $this->getFullConfig();
        unset($config['group']['group_class']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessUserModelClassSet()
    {
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['user_class']);
        $loader->load(array($config), new ContainerBuilder());
    }

    public function testDisableRegistration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['registration'] = false;
        $loader->load(array($config), $this->configuration);
        $this->assertNotHasDefinition('fos_user.registration.form');
    }

    public function testDisableResetting()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['resetting'] = false;
        $loader->load(array($config), $this->configuration);
        $this->assertNotHasDefinition('fos_user.resetting.form');
    }

    public function testDisableProfile()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['profile'] = false;
        $loader->load(array($config), $this->configuration);
        $this->assertNotHasDefinition('fos_user.profile.form');
    }

    public function testDisableChangePassword()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['change_password'] = false;
        $loader->load(array($config), $this->configuration);
        $this->assertNotHasDefinition('fos_user.change_password.form');
    }

    public function testUserLoadModelClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('Acme\MyBundle\Document\User', 'fos_user.model.user.class');
    }

    public function testUserLoadModelClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('Acme\MyBundle\Entity\User', 'fos_user.model.user.class');
    }

    public function testUserLoadManagerClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('fos_user.user_manager');
        $this->assertNotHasDefinition('fos_user.group_manager');
    }

    public function testUserLoadManagerClass()
    {
        $this->createFullConfiguration();

        $this->assertHasDefinition('fos_user.user_manager');
        $this->assertHasDefinition('fos_user.group_manager');
    }

    public function testUserLoadFormClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('FOS\UserBundle\Form\ProfileFormType', 'fos_user.profile.form.type.class');
        $this->assertParameter('FOS\UserBundle\Form\RegistrationFormType', 'fos_user.registration.form.type.class');
        $this->assertParameter('FOS\UserBundle\Form\ChangePasswordFormType', 'fos_user.change_password.form.type.class');
        $this->assertParameter('FOS\UserBundle\Form\ResettingFormType', 'fos_user.resetting.form.type.class');
    }

    public function testUserLoadFormClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('Acme\MyBundle\Form\ProfileFormType', 'fos_user.profile.form.type.class');
        $this->assertParameter('Acme\MyBundle\Form\RegistrationFormType', 'fos_user.registration.form.type.class');
        $this->assertParameter('Acme\MyBundle\Form\GroupFormType', 'fos_user.form.type.group.class');
        $this->assertParameter('Acme\MyBundle\Form\ChangePasswordFormType', 'fos_user.change_password.form.type.class');
        $this->assertParameter('Acme\MyBundle\Form\ResettingFormType', 'fos_user.resetting.form.type.class');
    }

    public function testUserLoadFormNameWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('fos_user_profile_form', 'fos_user.profile.form.name');
        $this->assertParameter('fos_user_registration_form', 'fos_user.registration.form.name');
        $this->assertParameter('fos_user_change_password_form', 'fos_user.change_password.form.name');
        $this->assertParameter('fos_user_resetting_form', 'fos_user.resetting.form.name');
    }

    public function testUserLoadFormName()
    {
        $this->createFullConfiguration();

        $this->assertParameter('acme_profile_form', 'fos_user.profile.form.name');
        $this->assertParameter('acme_registration_form', 'fos_user.registration.form.name');
        $this->assertParameter('acme_group_form', 'fos_user.form.group.name');
        $this->assertParameter('acme_change_password_form', 'fos_user.change_password.form.name');
        $this->assertParameter('acme_resetting_form', 'fos_user.resetting.form.name');
    }

    public function testUserLoadFormServiceWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('fos_user.profile.form');
        $this->assertHasDefinition('fos_user.registration.form');
        $this->assertNotHasDefinition('fos_user.form.group');
        $this->assertHasDefinition('fos_user.change_password.form');
        $this->assertHasDefinition('fos_user.resetting.form');
    }

    public function testUserLoadFormService()
    {
        $this->createFullConfiguration();

        $this->assertHasDefinition('fos_user.profile.form');
        $this->assertHasDefinition('fos_user.registration.form');
        $this->assertHasDefinition('fos_user.form.group');
        $this->assertHasDefinition('fos_user.change_password.form');
        $this->assertHasDefinition('fos_user.resetting.form');
    }

    public function testUserLoadConfirmationEmailWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter(false, 'fos_user.registration.confirmation.enabled');
        $this->assertParameter(array('webmaster@example.com' => 'webmaster'), 'fos_user.registration.confirmation.from_email');
        $this->assertParameter('FOSUserBundle:Registration:email.txt.twig', 'fos_user.registration.confirmation.template');
        $this->assertParameter('FOSUserBundle:Resetting:email.txt.twig', 'fos_user.resetting.email.template');
        $this->assertParameter(array('webmaster@example.com' => 'webmaster'), 'fos_user.resetting.email.from_email');
        $this->assertParameter(86400, 'fos_user.resetting.token_ttl');
    }

    public function testUserLoadConfirmationEmail()
    {
        $this->createFullConfiguration();

        $this->assertParameter(true, 'fos_user.registration.confirmation.enabled');
        $this->assertParameter(array('register@acme.org' => 'Acme Corp'), 'fos_user.registration.confirmation.from_email');
        $this->assertParameter('AcmeMyBundle:Registration:mail.txt.twig', 'fos_user.registration.confirmation.template');
        $this->assertParameter('AcmeMyBundle:Resetting:mail.txt.twig', 'fos_user.resetting.email.template');
        $this->assertParameter(array('reset@acme.org' => 'Acme Corp'), 'fos_user.resetting.email.from_email');
        $this->assertParameter(1800, 'fos_user.resetting.token_ttl');
    }

    public function testUserLoadTemplateConfigWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('twig', 'fos_user.template.engine');
        $this->assertParameter('FOSUserBundle::form.html.twig', 'fos_user.template.theme');
    }

    public function testUserLoadTemplateConfig()
    {
        $this->createFullConfiguration();

        $this->assertParameter('php', 'fos_user.template.engine');
        $this->assertParameter('AcmeMyBundle:Form:theme.html.twig', 'fos_user.template.theme');
    }

    public function testUserLoadEncoderConfigWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('sha512', 'fos_user.encoder.algorithm');
        $this->assertParameter(false, 'fos_user.encoder.encode_as_base64');
        $this->assertParameter(1, 'fos_user.encoder.iterations');
    }

    public function testUserLoadEncoderConfig()
    {
        $this->createFullConfiguration();

        $this->assertParameter('sha1', 'fos_user.encoder.algorithm');
        $this->assertParameter(true, 'fos_user.encoder.encode_as_base64');
        $this->assertParameter(3, 'fos_user.encoder.iterations');
    }

    public function testUserLoadUtilServiceWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertAlias('fos_user.mailer.default', 'fos_user.mailer');
        $this->assertAlias('fos_user.util.email_canonicalizer.default', 'fos_user.util.email_canonicalizer');
        $this->assertAlias('fos_user.util.username_canonicalizer.default', 'fos_user.util.username_canonicalizer');
    }

    public function testUserLoadUtilService()
    {
        $this->createFullConfiguration();

        $this->assertAlias('acme_my.mailer', 'fos_user.mailer');
        $this->assertAlias('acme_my.email_canonicalizer', 'fos_user.util.email_canonicalizer');
        $this->assertAlias('acme_my.username_canonicalizer', 'fos_user.util.username_canonicalizer');
    }

    /**
     * @return ContainerBuilder
     */
    protected function createEmptyConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    /**
     * @return ContainerBuilder
     */
    protected function createFullConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getFullConfig();
        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    /**
     * getEmptyConfig
     *
     * @return array
     */
    protected function getEmptyConfig()
    {
        $yaml = <<<EOF
db_driver: mongodb
firewall_name: fos_user
user_class: Acme\MyBundle\Document\User
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    protected function getFullConfig()
    {
        $yaml = <<<EOF
db_driver: orm
firewall_name: fos_user
use_listener: true
user_class: Acme\MyBundle\Entity\User
from_email: { admin@acme.org: Acme Corp } # 1st solution
profile:
    form:
        type: Acme\MyBundle\Form\ProfileFormType
        handler: Acme\MyBundle\Form\ProfileFormHandler
        name: acme_profile_form
        validation_groups: [acme_profile]
change_password:
    form:
        type: Acme\MyBundle\Form\ChangePasswordFormType
        handler: Acme\MyBundle\Form\ChangePasswordFormHandler
        name: acme_change_password_form
        validation_groups: [acme_change_password]
registration:
    confirmation:
        from_email: { register@acme.org: Acme Corp } # 2nd solution
        enabled: true
        template: AcmeMyBundle:Registration:mail.txt.twig
    form:
        type: Acme\MyBundle\Form\RegistrationFormType
        handler: Acme\MyBundle\Form\RegistrationFormHandler
        name: acme_registration_form
        validation_groups: [acme_registration]
resetting:
    token_ttl: 1800
    email:
        from_email: { reset@acme.org: Acme Corp } # 2nd solution
        template: AcmeMyBundle:Resetting:mail.txt.twig
    form:
        type: Acme\MyBundle\Form\ResettingFormType
        handler: Acme\MyBundle\Form\ResettingFormHandler
        name: acme_resetting_form
        validation_groups: [acme_resetting]
service:
    mailer: acme_my.mailer
    email_canonicalizer: acme_my.email_canonicalizer
    username_canonicalizer: acme_my.username_canonicalizer
encoder:
    algorithm: sha1
    encode_as_base64: true
    iterations: 3
template:
    engine: php
    theme: AcmeMyBundle:Form:theme.html.twig
group:
    group_class: Acme\MyBundle\Entity\Group
    form:
        type: Acme\MyBundle\Form\GroupFormType
        handler: Acme\MyBundle\Form\GroupHandler
        name: acme_group_form
        validation_groups: [acme_group]
EOF;
        $parser = new Parser();

        return  $parser->parse($yaml);
    }

    private function assertAlias($value, $key)
    {
        $this->assertEquals($value, (string) $this->configuration->getAlias($key), sprintf('%s alias is correct', $key));
    }

    private function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    private function assertHasDefinition($id)
    {
        $this->assertTrue(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    private function assertNotHasDefinition($id)
    {
        $this->assertFalse(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    protected function tearDown()
    {
        unset($this->configuration);
    }

}

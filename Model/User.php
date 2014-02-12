<?php

namespace Bigfoot\Bundle\UserBundle\Model;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Serializable;

use Bigfoot\Bundle\UserBundle\Entity\Role;

/**
 * User
 */
abstract class User implements AdvancedUserInterface, Serializable
{
    const ROLE_DEFAULT = 'ROLE_USER';

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    protected $username;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=6)
     */
    protected $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups = {"Register", "ChangePassword", "ForgotPassword"})
     * @Assert\Length(
     *     min = "6",
     *     max = "150",
     *     groups = {"Register", "ChangePassword", "ForgotPassword"})
     * )
     *
     */
    protected $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string")
     */
    protected $salt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    protected $lastLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="confirmation_token", type="string", nullable=true)
     */
    protected $confirmationToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
     */
    protected $passwordRequestedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean")
     */
    protected $locked;

    /**
     * @var boolean
     *
     * @ORM\Column(name="expired", type="boolean")
     */
    protected $expired;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     */
    protected $expiresAt;

    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinTable(name="bigfoot_user_role")
     */
    protected $roles;

    public function __construct()
    {
        $this->salt    = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->roles   = new ArrayCollection();
        $this->enabled = false;
        $this->locked  = false;
        $this->expired = false;
    }

    /**
     * Add Role.
     *
     * @return BigfootUser
     */
    public function addRole(Role $role)
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function getRoles()
    {
        foreach ($this->roles as $role) {
            $roles[] = $role->getName();
        }

        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    public function getFormRoles()
    {
        return $this->roles;
    }

    /**
     * Remove a User Role.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $roles
     * @return BigfootUser
     */
    public function removeRole($role)
    {
        $this->roles->removeElement($role);

        return $this;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return BigfootUser
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return BigfootUser
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function setExpired($boolean)
    {
        $this->expired = (Boolean) $boolean;

        return $this;
    }

    public function setExpiresAt(\DateTime $date = null)
    {
        $this->expiresAt = $date;

        return $this;
    }

    public function setLastLogin(\DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    public function setLocked($boolean)
    {
        $this->locked = $boolean;

        return $this;
    }

    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
               $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function setEnabled($boolean)
    {
        $this->enabled = (Boolean) $boolean;

        return $this;
    }

    public function isEnabled()
    {
        return true;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize() {}

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized) {}
}
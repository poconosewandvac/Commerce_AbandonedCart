<?php

namespace PoconoSewVac\AbandonedCart\Frontend;

use PoconoSewVac\AbandonedCart\Repositories\UserRepository;

/**
 * Class Unsubscribe
 * @package PoconoSewVac\AbandonedCart\Frontend
 */
final class UnsubscribeUser
{
    /**
     * @var \Commerce
     */
    protected $commerce;

    /**
     * @var \modmore\Commerce\Adapter\AdapterInterface|\modmore\Commerce\Adapter\Revolution
     */
    protected $adapter;

    /**
     * @var string $email
     */
    protected $email;

    /**
     * @var \AbandonedCartUser $user
     */
    protected $user;

    /**
     * @var MessageList $messages
     */
    public $messages;

    /**
     * @var MessageList $errors
     */
    public $errors;

    /**
     * UnsubscribeUser constructor.
     * @param \Commerce $commerce
     * @param string $email
     * @param \AbandonedCartUser|null $user
     */
    public function __construct(\Commerce $commerce, $email, ?\AbandonedCartUser $user)
    {
        $this->commerce = $commerce;
        $this->adapter = $commerce->adapter;
        $this->email = $email;
        $this->user = $user;
        $this->messages = new MessageList();
        $this->errors = new MessageList();
    }

    /**
     * Create an instance of UnsubscribeUser
     *
     * @param \Commerce $commerce
     * @param $email
     * @return self
     */
    public static function fromEmail(\Commerce $commerce, $email)
    {
        $userRepository = new UserRepository($commerce);
        $user = $userRepository->getByEmail($email);

        return new self($commerce, $email, $user);
    }

    /**
     * Determine if user can be unsubscribed from Abandoned Cart emails
     *
     * @return bool
     */
    public function canBeUnsubscribed()
    {
        if (strlen((string) $this->email) < 1) {
            // Email address not entered, don't display any error
            return false;
        }

        // User not found
        if (!$this->user) {
            $this->errors->add($this->adapter->lexicon('commerce_abandonedcart.not_found'));
            return false;
        }

        // User already unsubscribed
        if (!$this->user->isSubscribed()) {
            $this->errors->add($this->adapter->lexicon('commerce_abandonedcart.already_unsubscribed'));
            return false;
        }

        return true;
    }

    /**
     * Unsubscribe the user from Abandoned Cart emails
     *
     * @return void
     */
    public function unsubscribe()
    {
        $this->user->unsubscribe();
        $this->messages->add($this->adapter->lexicon('commerce_abandonedcart.unsubscribed_from_emails'));
    }

    /**
     * Render the unsubscribe page output
     *
     * @return string
     * @throws \modmore\Commerce\Exceptions\ViewException
     */
    public function view()
    {
        $view = $this->commerce->view();
        $baseUser = $this->user ? $this->user->getUser() : null;

        $output = $view->render('abandonedcart/frontend/unsubscribe.twig', [
            'email' => $this->email,
            'user' => $baseUser ? $baseUser->toArray() : [],
            'abandonedcart_user' => $this->user ? $this->user->toArray() : [],
            'site_url' => $this->adapter->getOption('site_url'),
            'errors' => $this->errors->getAll(),
            'messages' => $this->messages->getAll(),
        ]);

        return $this->adapter->parseMODXTags($output);
    }
}
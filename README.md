# ViewBundle Bundle

[![Latest Version](https://img.shields.io/github/release/ThrusterIO/view-bundle.svg?style=flat-square)](https://github.com/ThrusterIO/view-bundle/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/ThrusterIO/view-bundle.svg?style=flat-square)](https://travis-ci.org/ThrusterIO/view-bundle)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ThrusterIO/view-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/ThrusterIO/view-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/ThrusterIO/view-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/ThrusterIO/view-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/thruster/view-bundle.svg?style=flat-square)](https://packagist.org/packages/thruster/view-bundle)

The Thruster ViewBundle Bundle. A simple addition to Symfony to provide ability to define views for simple data mappings similar to [Elixir](http://elixir-lang.org/) [Phoenix Views](http://www.phoenixframework.org/docs/views).


## Install

Via Composer

```bash
$ composer require thruster/view-bundle
```

Enable Bundle

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Thruster\Bundle\ViewBundle(),
        // ...
    );
}
```

## Usage

Views works similary to Symfony Controllers, all view classes **should** resident in `Bundle/View/` folder. [Some real life examples](EXAMPLES.md)

### View

```php
<?php
// src/AppBundle/View/DefaultView.php

namespace AppBundle\View;

use Thruster\Bundle\ViewsBundle\View\View;
use AppBundle\Entity\User;

class DefaultView extends View
{
    public function welcome($name)
    {
        return [
            'msg' => 'Hello, ' . $name
        ];
    }
    
    public function me(User $user)
    {
    	  return [
    	    'name' => $user->getName(),
    	    'email' => $user->getEmail()
    	  ];
   	 }
   	 
   	 public function friend(User $friend)
   	 {
   	 	  $friend = $this->renderOne([$this, 'me'], $friend);
   	 	  // Also possible just $this->me($friend);
   	 	  
   	 	  unset($friend['email']);
   	 	  
   	 	  $friend['items'] = $this->renderMany('AppBundle:Item:public_view', $friend->getItems());
   	 	  
   	 	  return $friend;
   	 }
   	 
   	 public function friends(array $friends)
   	 {
   	 	  return [
   	 	      'data' => $this->renderMany([$this, 'friend'], $friends)
   	 	  ];
   	 }
}
```

### Controller

```php
<?php

namespace AppBundle\Controller;

use Thruster\Bundle\ViewsBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->jsonView('welcome', 'guest');
    }
    
    public function meAction()
    {
    	return $this->jsonView('AppBundle:Default:me', $this->getUser());
    }
    
    public function friendAction($id)
    {
    	$friend = $this->getRepository('AppBundle:User')->find($id);
    	
    	$data = $this->view('AppBundle\View\DefaultView::friend', $friend);
    	
    	return new JsonReponse($data);
    }
    
    public function friendsAction()
    {
    	$friends = $this->getRepository('AppBundle:User')->findAll();
    	
    	return $this->jsonView('friends', $friends);
    }    
}
```


## Testing

```bash
$ composer test
```


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.


## License

Please see [License File](LICENSE) for more information.

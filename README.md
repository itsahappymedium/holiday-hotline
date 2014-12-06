Happy Medium's Holiday Hotline
===============

Happy Medium's Holiday Hotline was built in 2014 as a fun project using the [Twilio API](https://www.twilio.com/docs/api).

## Demo

Call the following number to see how it works:

```
515-532-5541
```

## Setup

To get the hotline running on your own machine or server, fork it and clone it to your local machine.

Navigate to your local version, and install dependences from both [PHP Composer](http://getcomposer.org/)
and [NPM](http://npmjs.org):

```sh
$ composer install
$ npm install
```

## Deployment

To deploy your server, you can set the deployment path on lines 18 and 19 of `Gruntfile.js` (be sure you have SSH access), and run:

```sh
$ grunt deploy
```

The next step is to tell Twilio that you want to use the endpoint on your new server for Voice calls.

Let's assume you've deployed your hotline to `http://hotline.yoursite.com/hotline.php`. Create an account on [Twilio][http://twilio.com] and find your [Numbers](https://www.twilio.com/user/account/phone-numbers/incoming) section.

Click on the phone number given to you after having created an account, and update the Voice Request URL to match the ULR of your deployed hotline.

Then, give your Twilio phone number a ring, and see what happens!

_Note: In order to get rid of the demo message at the beginning of every call, you'll want to upgrade to a paid plan._
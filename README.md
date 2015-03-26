# OctoberSocialite

This Plugin is under construction and is a Test Project. Can not be used in production environment !!!

# Installation

1. create a sub folder kakuki under plugins directory.
2. cd in folder kakuki and clone the project as oauth2 (Important because of namespace)

```git clone https://github.com/deroccha/OctoberSocialite.git oauth2```

3. run ```composer update``` in the root of Plugin directory to install dependencies
4. install plugin with php artisan ``php artisan plugin:install Kakuki.OAuth2``
5. In Backend under Settings you should be able to configure Providers under Socialite like defining the oauth credentials per provider.
   At this stage Facebook are Google are working but not fully tested
6. Drag and drop the component on a page   
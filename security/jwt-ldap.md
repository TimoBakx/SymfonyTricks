# Combining JWT and LDAP

## Requirements
For JWT, I use [lexik/jwt-authentication-bundle](https://packagist.org/packages/lexik/jwt-authentication-bundle).
For LDAP, I use [ldaptools/ldaptools-bundle](https://packagist.org/packages/ldaptools/ldaptools-bundle).

Both can be installed via Composer:
```
composer req lexik/jwt-authentication-bundle ldaptools/ldaptools-bundle
```

## Configuration
Follow the instructions for configuring both packages as normal:
- JWT: [Configuration](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#configuration)
- LDAP: [Getting started](https://github.com/ldaptools/ldaptools-bundle#getting-started)

After that, you can add the LDAP guard to the `login` firewall in your `security.yaml` file:
```yaml
security:
    # ...
    
    firewalls:

        login:
            pattern:  ^/api/login
            stateless: true
            anonymous: true
            form_login:
                check_path:               /api/login_check
                success_handler:          lexik_jwt_authentication.handler.authentication_success
                failure_handler:          lexik_jwt_authentication.handler.authentication_failure
                require_previous_session: false
            guard:
                authenticators:
                    - ldap_tools.security.ldap_guard_authenticator
```

## Using `json_login` instead of `form_login`
I ran into some problems when using the `json_login` authentication instead of the `form_login` method used above.
The LDAP Guard Authenticator provided by LDAP Tools can not read the credentials from a JSON encoded POST content.

I created a [`JsonLdapGuardAuthenticator`](jwt-ldap/JsonLdapGuardAuthenticator.php) class that extends the original
`LdapGuardAuthenticator` of the LDAP Tools. This class [overrides the `getRequestParameter`](jwt-ldap/JsonLdapGuardAuthenticator.php#L53) method to pull parameters out
of the JSON POST content.

I copied the service defintion of the original `LdapGuardAuthenticator`.
To prevent my JWT requests from being redirected to a (non-existing) login form, I had to use some handlers from the 
JWT package instead of the default ones from the LDAP package.

```yaml
services:
    App\Authentication\Ldap\JsonLdapGuardAuthenticator:
        arguments:
            - '%security.authentication.hide_user_not_found%'
            - '@ldap_tools.security.user.ldap_user_checker'
            - '@ldap_tools.ldap_manager'
            - '@lexik_jwt_authentication.security.guard.jwt_token_authenticator' # Instead of '@ldap_tools.security.authentication.form_entry_point'
            - '@event_dispatcher'
            - '@lexik_jwt_authentication.handler.authentication_success' # Instead of '@ldap_tools.security.auth_success_handler'
            - '@lexik_jwt_authentication.handler.authentication_failure' # Instead of '@ldap_tools.security.auth_failure_handler'
            - '%ldap_tools.security.guard.options%'
            - '@ldap_tools.security.user.ldap_user_provider'
```

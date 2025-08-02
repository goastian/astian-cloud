<!--
 - SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 - SPDX-FileCopyrightText: 2013-2016 ownCloud, Inc.
 - SPDX-License-Identifier: AGPL-3.0-or-later
-->

# Elyerr Cloud (Custom Fork of Nextcloud) ☁

[![REUSE status](https://api.reuse.software/badge/github.com/nextcloud/server)](https://api.reuse.software/info/github.com/nextcloud/server)
[![codecov](https://codecov.io/gh/nextcloud/server/branch/master/graph/badge.svg)](https://codecov.io/gh/nextcloud/server)
[![CII Best Practices](https://bestpractices.coreinfrastructure.org/projects/209/badge)](https://bestpractices.coreinfrastructure.org/projects/209)
[![Design](https://contribute.design/api/shield/nextcloud/server)](https://contribute.design/nextcloud/server)

**Elyerr Cloud** is a customized version of the Nextcloud Server, designed to integrate with centralized authentication systems via OAuth2, while maintaining full compatibility with the AGPL-3.0 license and credits to the original project.

---

## ℹ️ About this version

This fork has been **adapted and extended** specifically to work with [`oauth2-passport-server`](https://github.com/elyerr/oauth2-passport-server), enabling an advanced federated authentication and service-based access control system.

It is ideal for environments that require:

-   Centralized and delegated authentication (SSO).
-   Integration with external identities through OpenID Connect.
-   Custom login flow handling in Nextcloud.

## 🔐 Session and Authentication Requirements

This version **explicitly requires** the [`user_oidc`](https://github.com/elyerr/user_oidc) plugin to be **installed and enabled**.

Additionally, to properly establish a session through federated login, an OAuth2-compliant server is required:

-   [`oauth2-passport-server`](https://github.com/elyerr/oauth2-passport-server) (**recommended**) that implements standard OpenID Connect flows.

### 🛠 Required Configuration

To ensure login works correctly, you must to Add the following to your `config/config.php`:

```php
'trusted_domains' => [
    'https://aouth2.domain.xyz' // oauth-passport-server domain
  ],
 'user_oidc' => [
    'httpclient.allowselfsigned' => true, // Olny dev mode
    'prompt' => 'internal',
    'store_login_token' => true,
  ],
  'oauth2_passport_server' => [
    'master' => 'https://aouth2.domain.xyz', // oauth-passport-server domain
    'httpclient.allowselfsigned' => true, // Olny dev mode
  ],
  'allow_local_remote_servers' => true,
```

# 📜 License

This project is licensed under the GNU Affero General Public License v3.0 (AGPL-3.0).

According to the AGPL, if you modify this application and offer it as a service (SaaS), you must make the source code (including your modifications) publicly available to your users.

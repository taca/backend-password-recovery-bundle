<?php

declare(strict_types=1);

/*
 * This file is part of Backend Password Recovery Bundle.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/backend-password-recovery-bundle
 */

/*
 * Errors
 */
$GLOBALS['TL_LANG']['ERR']['pwRecoveryFailed'] = 'No user with this username or this e-mail address was found in the user database.';

/*
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['MSC']['recoverPassword'] = 'パスワードの回復に進む';
$GLOBALS['TL_LANG']['MSC']['pwRecoveryHeadline'] = 'パスワードの回復';
$GLOBALS['TL_LANG']['MSC']['usernameOrEmailPlaceholder'] = '電子メールまたはユーザー名';
$GLOBALS['TL_LANG']['MSC']['usernameOrEmailExplain'] = '電子メールアドレスまたはユーザー名を入力するとパスワードの回復のリンクを含んだ電子メールのメッセージを受信できます。';
$GLOBALS['TL_LANG']['MSC']['forgotPassword'] = 'パスワード忘れ';
$GLOBALS['TL_LANG']['MSC']['pwRecoveryLinkSuccessfullySent'] = 'まもなくパスワードを回復の仕方の手順の電子メールが届きます。受信箱に届かない場合は迷惑メールの確認もしてください。';
// Email subject
$GLOBALS['TL_LANG']['MSC']['pwRecoveryEmailSubject'] = '#host#でパスワード要求';
// Email text
$GLOBALS['TL_LANG']['MSC']['pwRecoveryEmailText'] = '
こんにちは#name#さん。

#host#で新しいパスワード設定の依頼がありました。

新しいパスワードを設定するには次のリンクを開いてください。

#link# 

このメールを依頼した覚えがない場合は、ウェブサイトの管理者に連絡してください。



---------------------------------

これは自動生成したメッセージです。返信しないようにお願いします。
';

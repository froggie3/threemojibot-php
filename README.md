# Three Random Hiragana Bot

Thank you for reading this document.

## Introduction

This project is made as a PHP fork for [ひらがな3文字を毎分ランダムにつぶやくbot](https://twitter.com/3hiragana_bot) on Twitter, where it tries to generate three random hiragana characters, and then posts them on Twitter. The concept was so amazing but rediculous that I got inspirated to make this. While this bot will NOT tries to post on Twitter it has capable of posting on a specific Discord channel that you specifies on `./config/webhook.txt` through Webhook protocol. 

## Contents

-   `./config/webhook_url.txt`
    -   To paste the Discord Webhook URL here directly allows the program to find where to send the content. `webhook.php` requires this file.
-   `./script.php`
    -   The core file to be executed.

## Requisites

-   Tested and developed on PHP 8.0+
-   No worries! This is based on 100% *pure and authentic* PHP scripting

```bash
php -v
PHP 8.1.10 (cli) (built: Sep  3 2022 04:38:10) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.1.10, Copyright (c) Zend Technologies
```

## Options 

With no option, the script shows the generated word with HTML-encoded so that your web browser can understand.

-   `-w` shows the generated characters on your terminal (expected for testing this script).
-   `-p` posts the generated characters on a specific Discord channel that you specifies in `./config/webhook_url.txt`.
-   `-r` specifies how many times generation is iterated.

Example:
```
> php .\script.php -w -r 6
ふめぅ
ごっで
だざげ
まがわ
ししん
ぬびむ
```

## Usage

1.  Clone this repository

```bash
git clone https://github.com/froggie3/threemojibot-php.git
```

2.  Change your current directory

```bash
cd threemojibot-php
```

3.  Duplicate the original webhook file and rename it 

```bash
cd config
cp webhook_url.bak.txt webhook_url.txt
```

4.  Write your Webhook URL

```bash
vim webhook_url.txt
```

5.  Configure your cronjob or systemd so `script.php` regularly runs

```bash
crontab -e
*/5 * * * * php -f "/path/to/script.php" 
```

## References

-   ひらがな3文字を毎分ランダムにつぶやくbot  
[https://twitter.com/3hiragana_bot](https://twitter.com/3hiragana_bot) 

    

Have a fun!

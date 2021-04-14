# -*- coding: utf-8 -*-
#!/usr/bin/env python2.7
#API Portal Description
"""
==============================================================================
created    : 

Last update: 04/21/2017
 
Developer: Wei-Chun Chang 
 
Lite Version 1 @Yishan08032019
 
Filename: authentic_utility.py
 
==============================================================================
"""

import sys
import hashlib
import hmac
import base64
import gnupg
import os, sys, inspect

import web_utility

# {{{ def encrypt_pgp(key_loc, message, logpath):
def encrypt_pgp(key_loc, message, logpath):
    '''
    Encrypt message using pgp
    param: key_loc Path to public key
    param: message String to be encrypted
    return base64 encoded encrypted string
    '''

    try:
        gpg = gnupg.GPG(homedir=key_loc)

        public_key = gpg.list_keys()
        encrypted_data = gpg.encrypt(message, public_key[0]['fingerprint'], \
                                    always_trust = True)
        encrypted_string = str(encrypted_data)

        return base64.urlsafe_b64encode(encrypted_string)

    except Exception as err:
        web_utility.exception_report(str(err), logpath)
        return "encrypt_pgp_fail:{}".format(str(err))
    finally:
        pass
# }}}

# {{{ def decrypt_pgp(key_loc, code, package, logpath):
def decrypt_pgp(key_loc, code, package, logpath):

    try:
        gpg = gnupg.GPG(homedir=key_loc)

        private_key = gpg.list_keys(True)
        encrypted_string = base64.urlsafe_b64decode(package)
        decrypted_data = gpg.decrypt(encrypted_string, passphrase=code)
        if str(decrypted_data) == '': 
            err_msg = 'No data'
            raise ValueError(err_msg)
        return str(decrypted_data)
    except Exception as err:
        web_utility.exception_report(str(err), logpath)
        return "decrypt_pgp_fail:{}".format(str(err))
    finally:
        pass
# }}}

# {{{ def calculate_signature(key_loc, string_to_sign, logpath):
def calculate_signature(key_loc, string_to_sign, logpath):
    '''
    @summary: calculate HMAC-SHA signature
    '''

    try:
        print "~~here auth~~"
        print key_loc
        #gpg = gnupg.GPG(homedir=key_loc)
        gpg = gnupg.GPG(gnupghome=key_loc,gpgbinary="/usr/bin/gpg")
        print gpg

        public_key = gpg.list_keys()
        print public_key
        secret_key = str(public_key[0]['fingerprint'])
        print secret_key
        sig = base64.urlsafe_b64encode(hmac.new(secret_key, string_to_sign, hashlib.sha1).digest()) 
        print sig
        return sig
    except Exception as err:
        web_utility.exception_report(str(err), logpath)
        return "calculate_signature_fail:{}".format(str(err))
    finally:
        pass
# }}}

# {{{ def check_token(key_loc, code, token, \
def check_token(key_loc, code, token, \
                vendor_id, secret, current_timestamp, timeout_value, logpath):
    '''
    @summary: check token generated by end user, return True or False
            condition:
            should be decrypted
            magic_string == secret
            vendor_key == vendor_id
            diff_minutes > timeout_value
            diff_minutes < -2
    '''
    try:
        decrypted_str = decrypt_pgp(key_loc, code, token, logpath)
        #if decrypted_str == False:
        if 'decrypt_pgp_fail' in decrypted_str:
            err_msg = 'decrypt token fail'
            raise Exception(err_msg)

        vendor_key, magic_string, real_timestamp = decrypted_str.split('#')
        diff_minutes = (int(current_timestamp) - int(real_timestamp)) / 60

        if magic_string != secret:
            err_msg = 'cannot find magic string'
            raise Exception(err_msg)
        elif vendor_key != vendor_id:
            err_msg = 'vendor id is not correct'
            raise Exception(err_msg)
        elif diff_minutes > timeout_value:
            err_msg = 'session timeout'
            raise Exception(err_msg)
        elif diff_minutes < -2:
            err_msg = 'client timestamp is greater than cms timestamp'
            raise Exception(err_msg)
        else:
            return True,"ok"
    except Exception as err:
        import sys
        err2 = str(sys.exc_info()[1][0])
        if(decrypted_str):
            err2 += "\ndecrypted_str=%s"%(decrypted_str)
        if(token):
            err2 += "\ninput token =%s"%(token)
            err2 += "\ndecoded token = %s"%(base64.urlsafe_b64decode(token))
        web_utility.exception_report(str(err), logpath)
        return False, str(err) + "\n" + err2
# }}}

# {{{ def check_session_id(key_loc, code, session_id, \
def check_session_id(key_loc, code, session_id, \
                    secret, current_timestamp, timeout_value, logpath):
    '''
    @summary: check session_id generated by server, return True or False
            condition:
            should be decrypted
            magic_string == secret
            diff_minutes > timeout_value
            diff_minutes < 0
    '''

    try:
        decrypted_str = decrypt_pgp(key_loc, code, str(session_id), logpath)
        web_utility.note_report('[decrypted_str]: %s' % decrypted_str, \
                                logpath)
        
        #if decrypted_str == False:
        if 'decrypt_pgp_fail' in decrypted_str:
            err_msg = 'decrypt session_id fail'
            raise Exception(err_msg)

        magic_string, client_timestamp = decrypted_str.split('*')
        
        diff_minutes = (int(current_timestamp) - int(client_timestamp)) / 60

        if magic_string != secret:
            err_msg = 'cannot find magic string'
            raise Exception(err_msg)
        elif diff_minutes > timeout_value:
            err_msg = 'session timeout'
            raise Exception(err_msg)
        elif diff_minutes < -2:
            err_msg = 'client sent timestamp({}) is larger than server current timestamp({})'.format(client_timestamp,current_timestamp)
            raise Exception(err_msg)
        else:
            return True,"ok"
    except Exception as err:
        import logging
        logging.getLogger('fappYott').error("Check_session_id: {}-{}-{}-{}-{}-{}-{}-error:{}".format(key_loc,code, session_id, secret, current_timestamp, timeout_value, logpath, err))
        return False,str(err)

# }}}
@extends('emails.layout')

@section('body')
    <div class="layout one-col fixed-width" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000vw - 179160px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
        <div class="layout__inner" style="border-collapse: collapse;display: table;background-color: #ffffff;" emb-background-style>
            <!--[if (mso)|(IE)]><table align="center" cellpadding="0" cellspacing="0"><tr class="layout-fixed-width" emb-background-style><td style="width: 600px" class="w560"><![endif]-->
            <div class="column" style="text-align: left;color: #565656;font-size: 14px;line-height: 21px;font-family: Georgia,serif;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000vw - 179160px);">

                <div style="Margin-left: 20px;Margin-right: 20px;Margin-top: 24px;">
                    <h1 class="size-34" style="Margin-top: 0;Margin-bottom: 20px;font-style: normal;font-weight: normal;color: #565656;font-size: 30px;line-height: 38px;font-family: lato,tahoma,sans-serif;text-align: center;" lang="x-size-34"><span class="font-lato"><strong>Reset Your Password</strong></span></h1>
                </div>

                <div style="Margin-left: 20px;Margin-right: 20px;">
                    <p class="size-28" style="Margin-top: 0;Margin-bottom: 20px;font-family: cabin,avenir,sans-serif;font-size: 24px;line-height: 32px;text-align: center;" lang="x-size-28"><span class="font-cabin"><strong><span style="color:#1e9e37">{{ $email }}</span><span style="color:#111211">!</span></strong></span></p>
                </div>

                <div style="Margin-left: 20px;Margin-right: 20px;">
                    <p class="size-28" style="Margin-top: 0;Margin-bottom: 0;font-family: lato,tahoma,sans-serif;font-size: 24px;line-height: 32px;text-align: center;" lang="x-size-28"><span class="font-lato"><strong>You have requested for a password reset.</strong></span></p><p class="size-28" style="Margin-top: 20px;Margin-bottom: 20px;font-family: lato,tahoma,sans-serif;font-size: 24px;line-height: 32px;text-align: center;" lang="x-size-28"><span class="font-lato"><strong>Reset your password clicking the button below</strong></span></p>
                </div>

                <div style="Margin-left: 20px;Margin-right: 20px;">
                    <div style="line-height:20px;font-size:1px">&nbsp;</div>
                </div>

                <div style="Margin-left: 20px;Margin-right: 20px;Margin-bottom: 24px;">
                    <div class="btn fullwidth btn--shadow btn--large" style="text-align: center;">
                        <![if !mso]><a style="border-radius: 3px;display: block;font-size: 14px;font-weight: bold;line-height: 24px;padding: 12px 24px 13px 24px;text-align: center;text-decoration: none !important;transition: opacity 0.1s ease-in;color: #fff;box-shadow: inset 0 -2px 0 0 rgba(0, 0, 0, 0.2);background-color: #fc8600;font-family: Avenir, sans-serif;" href="{{ $url }}">Reset My Password</a><![endif]>
                        <!--[if mso]><p style="line-height:0;margin:0;">&nbsp;</p><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="{{ $url }}" style="width:560px" arcsize="7%" fillcolor="#FC8600" stroke="f"><v:shadow on="t" color="#CA6B00" offset="0,2px"></v:shadow><v:textbox style="mso-fit-shape-to-text:t" inset="0px,11px,0px,10px"><center style="font-size:14px;line-height:24px;color:#FFFFFF;font-family:sans-serif;font-weight:bold;mso-line-height-rule:exactly;mso-text-raise:4px">Verify My Account</center></v:textbox></v:roundrect><![endif]--></div>
                </div>

                <div style="Margin-left: 20px;Margin-right: 20px;">
                    Or Copy the link and paste it in your browser. {{$url}}
                </div>

            </div>
            <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
        </div>
    </div>

    <div style="line-height:20px;font-size:20px;">&nbsp;</div>

    <div class="layout one-col fixed-width" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000vw - 179160px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
        <div class="layout__inner" style="border-collapse: collapse;display: table;background-color: #f5f5f5;">
            <!--[if (mso)|(IE)]><table align="center" cellpadding="0" cellspacing="0"><tr class="layout-fixed-width" style="background-color: #f5f5f5;"><td style="width: 600px" class="w560"><![endif]-->
            <div class="column" style="text-align: left;color: #565656;font-size: 14px;line-height: 21px;font-family: Georgia,serif;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000vw - 179160px);">

                <div style="Margin-left: 20px;Margin-right: 20px;Margin-top: 24px;">
                    <div class="divider" style="display: block;font-size: 2px;line-height: 2px;Margin-left: auto;Margin-right: auto;width: 40px;background-color: #c8c8c8;Margin-bottom: 20px;">&nbsp;</div>
                </div>

                <div style="Margin-left: 20px;Margin-right: 20px;">
                    <p style="Margin-top: 0;Margin-bottom: 20px;font-family: avenir,sans-serif;text-align: center;"><span class="font-avenir"><strong>Yours, Nattiv Team.</strong></span></p>
                </div>

                <div style="Margin-left: 20px;Margin-right: 20px;Margin-bottom: 24px;">
                    <div class="divider" style="display: block;font-size: 2px;line-height: 2px;Margin-left: auto;Margin-right: auto;width: 40px;background-color: #c8c8c8;">&nbsp;</div>
                </div>

            </div>
            <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
        </div>
    </div>

    <div style="line-height:20px;font-size:20px;">&nbsp;</div>
@stop
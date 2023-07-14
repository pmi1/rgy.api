<table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse;">
    <tbody>
        @foreach ($orderItem as $i)
            <tr class="row-border-bottom">
              <th class="table-stack product-image-wrapper stack-column-center" width="1" style="mso-line-height-rule: exactly; border-bottom-width: 2px; border-bottom-color: #dadada; border-bottom-style: solid; padding: 13px 13px 13px 0;" bgcolor="#ffffff" valign="middle">
                 <a href="{{str_replace(env('DOMAIN'), env('MAIN_DOMAIN'), url($i->alias))}}"><img width="80" class="product-image" src="{{url('/imagecache/small/item/image/'.explode('#', $i->image)[0])}}" alt="" style="vertical-align: middle; text-align: center; width: 80px; max-width: 80px; height: auto !important; border-radius: 1px; padding: 0px;"></a>
              </th>
              <th class="product-details-wrapper table-stack stack-column" style="mso-line-height-rule: exactly; padding-top: 13px; padding-bottom: 13px; border-bottom-width: 2px; border-bottom-color: #dadada; border-bottom-style: solid;" bgcolor="#ffffff" valign="middle">
                <table cellspacing="0" cellpadding="0" border="0" width="100%" style="min-width: 100%;" role="presentation">
                  <tbody>
                    <tr>
                      <th class="line-item-description" style="mso-line-height-rule: exactly; font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Arial, "PT Sans"; font-size: 16px; line-height: 26px; font-weight: 400; color: #666363; padding: 13px 6px 13px 0;" align="left" bgcolor="#ffffff" valign="top">
                       <p style="mso-line-height-rule: exactly; font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Arial, "PT Sans"; font-size: 16px; line-height: 26px; font-weight: 400; color: #666363; margin: 0;" align="left">
                         <a href="{{str_replace(env('DOMAIN'), env('MAIN_DOMAIN'), url($i->alias))}}" style="text-decoration: none; color: #666363; font-size: 16px; line-height: 20px; font-family: Arial, Helvetica, sans-serif;">{{strip_tags($i->itemtype)}} <span style="display: block;">{{strip_tags($i->typename)}}</span></a>
                         <div class="muted" style="font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Arial, "PT Sans"; font-size: 14px; line-height: 26px; font-weight: normal; color: #999999; word-break: break-all;">
                           арт: {{strip_tags($i->article)}}
                         </div>
                       </p>
                      </th>

                      <th class="right line-item-qty" width="1" style="mso-line-height-rule: exactly; white-space: nowrap; padding: 13px 0 13px 13px;" align="right" bgcolor="#ffffff" valign="top">
                        <p style="mso-line-height-rule: exactly; font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Arial, "PT Sans"; font-size: 16px; line-height: 26px; font-weight: 400; color: #666363; margin: 0;" align="right">
                          {{round($i->day_price)}}&nbsp;р.&nbsp;×&nbsp;{{htmlspecialchars($i->quantity)}}
                        </p>
                      </th>

                      <th class="right line-item-line-price" width="1" style="mso-line-height-rule: exactly; white-space: nowrap; padding: 13px 0 13px 26px;" align="right" bgcolor="#ffffff" valign="top">
                        <p style="mso-line-height-rule: exactly; font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Arial, "PT Sans"; font-size: 16px; line-height: 26px; font-weight: 400; color: #666363; margin: 0;" align="right">
                          {{round($i->quantity * $i->day_price) }}&nbsp;р.
                        </p>
                      </th>
                    </tr>
                  </tbody>
                </table>
              </th>
            </tr>
        @endforeach
    </tbody>
</table>
,:∵⌒∵:,_,:∵⌒∵:,_,:∵⌒∵:,_,:∵⌒∵:,_,:∵⌒∵:,_,:∵⌒∵:,_,
　　　　<無料ユーザー　ステップメール> {$smarty.const.STEPMAIL_ENV}
,:∵⌒∵:,_,:∵⌒∵:,_,:∵⌒∵:,_,:∵⌒∵:,_,:∵⌒∵:,_,:∵⌒∵:,_

……………………………………………………………………………………………
　　【終了していないバッチ】
……………………………………………………………………………………………
送信日：{$smarty.now|date_format:'%Y/%m/%d %H:%M:%S'}
{foreach from=$MailData item=mailonedata}
┌─────────────────────────────────
│■ バッチクライアント：{$mailonedata.client_name|default:''}
└─────────────────────────────────
処理開始日時：{$mailonedata.process_startdate|default:''}
バッチID：{$mailonedata.batch_id|default:''}
バッチクラス：{$mailonedata.batch_class|default:''}
クライアントID：{$mailonedata.client_id|default:''}
メールタイプ：{$mailonedata.mailtype_title|default:''}
リスト名：{$mailonedata.list_name|default:''}

{/foreach}






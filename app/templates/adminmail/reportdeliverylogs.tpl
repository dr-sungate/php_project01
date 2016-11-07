,:∵⌒∵:,_,:∵⌒∵:,_,:∵⌒∵:,_,:∵⌒∵:,_,:∵⌒∵:,_,:∵⌒∵:,_,
　　　　<無料ユーザー　ステップメール> {$smarty.const.STEPMAIL_ENV}
,:∵⌒∵:,_,:∵⌒∵:,_,:∵⌒∵:,_,:∵⌒∵:,_,:∵⌒∵:,_,:∵⌒∵:,_

……………………………………………………………………………………………
　　【配信レポート】
……………………………………………………………………………………………
送信日：{$smarty.now|date_format:'%Y/%m/%d %H:%M:%S'}
{foreach from=$MailData item=mailonedata}
┌─────────────────────────────────
│■ 配信クライアント：{$mailonedata.client_name|default:''}
└─────────────────────────────────
クライアントID：{$mailonedata.client_id|default:''}
タスクID：{$mailonedata.task_id|default:''}
配信開始日時：{$mailonedata.process_date|default:''}
メールタイプ：{$mailonedata.mailtype_title|default:''}
配信結果：{$mailonedata.results|default:''}
配信リスト：
{$mailonedata.convertlistdata|default:''}

{/foreach}






$.extend(jQuery,
    {
        // json �i�ǤJ json �� JavaScript Object
        // container ����X���e���AjQuery Object
        JSONView:function (json, container) {
            var ob;
            if (typeof json == 'string')
                ob = JSON.parse(json);
            else
                ob = json;
            var p, l = [], c = container;
            var repeat = function (s, n) {  //���� s �r�� n ��
                return new Array(n + 1).join(s);
            };
            //���� JSON ���c��ƪ����j���
            //o     �ӷ�����
            //isar  ��ƬO true ���ܥN��o�@�����j���}�C���
            //s     ���j���h��
            var r = function (o, isar, s) {
                for (var n in o) {
                    var p = o[n];
                    switch (typeof p) {
                        case 'function':
                            break;
                        case 'string':
                            if (isar)
                                l.push({ Text:'<span class="jsonstring">"' + p + '"</span><span class="jsontag">,</span>', Step:s });
                            else
                                l.push({ Text:'<span class="jsonname">"' + n + '"</span><span class="jsontag">: </span><span class="jsonstring">"' + p + '"</span><span class="jsontag">,</span>', Step:s });
                            break;
                        case 'boolean':
                            if (isar)
                                l.push({ Text:'<span class="jsonboolean">"' + p + '"</span><span class="jsontag">,</span>', Step:s });
                            else
                                l.push({ Text:'<span class="jsonname">"' + n + '"</span><span class="jsontag">: </span><span class="jsonboolean">' + p + '</span><span class="jsontag">,</span>', Step:s });
                            break;
                        case 'number':
                            if (isar)
                                l.push({ Text:'<span class="jsonnumber">' + p + '</span><span class="jsontag">,</span>', Step:s });
                            else
                                l.push({ Text:'<span class="jsonname">"' + n + '"</span><span class="jsontag">: </span><span class="jsonnumber">' + p + '</span><span class="jsontag">,</span>', Step:s });
                            break;
                        case 'object':
                            if (p === null) {
                                if (isar)
                                    l.push({ Text:'<span class="jsonnull">' + p + '</span><span class="jsontag">,</span>', Step:s });
                                else
                                    l.push({ Text:'<span class="jsonname">"' + n + '"</span><span class="jsontag">: </span><span class="jsonnull">' + p + '</span><span class="jsontag">,</span>', Step:s });
                            }
                            else if (p.length == undefined) {
                                //object
                                if (!isar) {
                                    l.push({ Text:'<span class="jsonname">"' + n + '"</span><span class="jsontag">:</span>', Step:s });
                                }
                                l.push({ Text:'<span class="jsontag">{</span>', Step:s });
                                r(p, false, s + 1);
                                l.push({ Text:'<span class="jsontag">},</span>', Step:s });
                            }
                            else {
                                //array
                                if (!isar) {
                                    l.push({ Text:'<span class="jsonname">"' + n + '"</span><span class="jsontag">:</span>', Step:s });
                                }
                                l.push({ Text:'<span class="jsontag">[</span>', Step:s });
                                r(p, true, s + 1);
                                l.push({ Text:'<span class="jsontag">],</span>', Step:s });
                            }
                            break;
                        default:
                            break;
                    }
                }
                var last = l.pop();
                var ct = ',</span>';
                if (last.Text.substr(last.Text.length - ct.length) == ct)
                    l.push({ Text:last.Text.replace(ct, '</span>'), Step:last.Step });
                else
                    l.push(last);
            };

            //�N JavaScript Object �榡�ƶ�i array ��
            if (ob.length == undefined) {
                //object
                l.push({ Text:'<span class="jsontag">{</span>', Step:0 });
                r(ob, false, 1);
                l.push({ Text:'<span class="jsontag">}</span>', Step:0 });
            }
            else {
                //array
                l.push({ Text:'<span class="jsontag">[</span>', Step:0 });
                r(ob, true, 1);
                l.push({ Text:'<span class="jsontag">]</span>', Step:0 });
            }

            //�}�l��X
            var f = true;   //true���_�Ʀ�
            c.addClass('KelpJSONView');
            c.append('<ol></ol>');
            c = c.find('ol');
            for (var index in l) {
                var jobject = l[index];
                if (f) {
                    c.append($('<li class="jsonhighlight">' + repeat(' &nbsp; &nbsp;', jobject.Step) + jobject.Text + '</li>'));
                    f = false;
                }
                else {
                    c.append($('<li>' + repeat(' &nbsp; &nbsp;', jobject.Step) + jobject.Text + '</li>'));
                    f = true;
                }
            }
        }
    });


<a href="javascript:void(0);" title="复制" onClick="copyObj{{ $eleId ?? '' }}()"><i class="fa fa-copy"></i></a>

<script>

    function copyObj{{ $eleId ?? '' }}()
    {
        /* 复制 text或value*/

        @if($eleType == 'text')
            var Url2=document.getElementById("{{ $eleId ?? ''}}").innerText;
        @elseif($eleType == 'value')
            var Url2=document.getElementById("{{ $eleId ?? ''}}").value;
        @elseif($eleType == 'attr')
            var Url2=document.getElementById("{{ $eleId ?? ''}}").getAttribute("{{ $attr ?? 'data-attr' }}");
        @endif

        var oInput = document.createElement('input');
        oInput.value = Url2;
        document.body.appendChild(oInput);
        oInput.select(); // 选择对象
        document.execCommand("Copy"); // 执行浏览器复制命令
        oInput.className = 'oInput';
        oInput.style.display='none';
        layer.msg('复制成功');
    }

</script>
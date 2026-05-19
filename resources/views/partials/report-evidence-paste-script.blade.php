@once
<script>
window.ReportEvidencePaste = (function () {
    var state = {};

    function partId(prefix, suffix) {
        return prefix + '-' + suffix;
    }

    function compressEvidenceBlob(blob, callback) {
        if (!blob || !blob.type || blob.type.indexOf('image') === -1) {
            callback(null, 'Please paste an image file.');
            return;
        }
        if (blob.size > 12 * 1024 * 1024) {
            callback(null, 'Image is too large. Use a smaller screenshot (under 12 MB).');
            return;
        }

        var objectUrl = URL.createObjectURL(blob);
        var img = new Image();

        img.onload = function () {
            URL.revokeObjectURL(objectUrl);
            var maxDim = 800;
            var w = img.naturalWidth || 1;
            var h = img.naturalHeight || 1;
            if (w > maxDim || h > maxDim) {
                if (w >= h) {
                    h = Math.round((h * maxDim) / w);
                    w = maxDim;
                } else {
                    w = Math.round((w * maxDim) / h);
                    h = maxDim;
                }
            }
            var canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            var ctx = canvas.getContext('2d', { alpha: false });
            if (!ctx) {
                callback(null, 'Could not process image.');
                return;
            }
            ctx.drawImage(img, 0, 0, w, h);
            var quality = 0.72;
            var out = '';
            try {
                out = canvas.toDataURL('image/jpeg', quality);
                while (out.length > 320000 && quality > 0.4) {
                    quality -= 0.08;
                    out = canvas.toDataURL('image/jpeg', quality);
                }
            } catch (err) {
                callback(null, 'Could not compress image.');
                return;
            }
            img.onload = img.onerror = null;
            setTimeout(function () { callback(out, null); }, 0);
        };

        img.onerror = function () {
            URL.revokeObjectURL(objectUrl);
            img.onload = img.onerror = null;
            callback(null, 'Could not read pasted image.');
        };

        img.src = objectUrl;
    }

    function setLoading(prefix, isLoading) {
        var loading = document.getElementById(partId(prefix, 'loading'));
        var dropzone = document.getElementById(partId(prefix, 'dropzone'));
        if (loading) loading.classList.toggle('hidden', !isLoading);
        if (dropzone) {
            dropzone.classList.toggle('pointer-events-none', isLoading);
            dropzone.classList.toggle('opacity-70', isLoading);
        }
    }

    function applyImage(prefix, dataUrl) {
        if (!state[prefix]) state[prefix] = { data: '' };
        state[prefix].data = dataUrl;
        var ph = document.getElementById(partId(prefix, 'placeholder'));
        var preview = document.getElementById(partId(prefix, 'preview'));
        var clearBtn = document.getElementById(partId(prefix, 'clear'));
        var dropzone = document.getElementById(partId(prefix, 'dropzone'));
        var evErr = document.getElementById(partId(prefix, 'error'));
        if (ph) ph.classList.add('hidden');
        if (preview) {
            preview.src = dataUrl;
            preview.classList.remove('hidden');
        }
        if (clearBtn) clearBtn.classList.remove('hidden');
        if (evErr) evErr.classList.add('hidden');
        if (dropzone) dropzone.classList.remove('border-red-500', 'ring-2', 'ring-red-400');
    }

    function processBlob(prefix, blob) {
        setLoading(prefix, true);
        setTimeout(function () {
            compressEvidenceBlob(blob, function (dataUrl, errMsg) {
                setLoading(prefix, false);
                if (errMsg) {
                    alert(errMsg);
                    return;
                }
                if (dataUrl) applyImage(prefix, dataUrl);
            });
        }, 10);
    }

    function init(prefix) {
        state[prefix] = { data: '' };
        var dropzone = document.getElementById(partId(prefix, 'dropzone'));
        var clearBtn = document.getElementById(partId(prefix, 'clear'));
        if (!dropzone) return;

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                reset(prefix);
            });
        }

        dropzone.addEventListener('paste', function (e) {
            var cd = e.clipboardData;
            if (!cd || !cd.items) return;
            for (var i = 0; i < cd.items.length; i++) {
                if (cd.items[i].type.indexOf('image') === -1) continue;
                e.preventDefault();
                var blob = cd.items[i].getAsFile();
                if (!blob) return;
                processBlob(prefix, blob);
                return;
            }
        });

        dropzone.addEventListener('dragover', function (e) {
            e.preventDefault();
            dropzone.classList.add('border-orange-500', 'bg-orange-50');
        });
        dropzone.addEventListener('dragleave', function () {
            dropzone.classList.remove('border-orange-500', 'bg-orange-50');
        });
        dropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropzone.classList.remove('border-orange-500', 'bg-orange-50');
            var f = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
            if (!f || f.type.indexOf('image') === -1) return;
            processBlob(prefix, f);
        });
    }

    function reset(prefix) {
        state[prefix] = { data: '' };
        var hidden = document.getElementById(prefix);
        var ph = document.getElementById(partId(prefix, 'placeholder'));
        var img = document.getElementById(partId(prefix, 'preview'));
        var clr = document.getElementById(partId(prefix, 'clear'));
        var loading = document.getElementById(partId(prefix, 'loading'));
        if (hidden) hidden.value = '';
        if (loading) loading.classList.add('hidden');
        if (ph) ph.classList.remove('hidden');
        if (img) {
            img.removeAttribute('src');
            img.classList.add('hidden');
        }
        if (clr) clr.classList.add('hidden');
        setLoading(prefix, false);
    }

    function syncHidden(prefix) {
        var hidden = document.getElementById(prefix);
        if (hidden) hidden.value = getData(prefix);
    }

    function getData(prefix) {
        return (state[prefix] && state[prefix].data) || '';
    }

    function markInvalid(prefix) {
        var evErr = document.getElementById(partId(prefix, 'error'));
        var dropzone = document.getElementById(partId(prefix, 'dropzone'));
        if (evErr) evErr.classList.remove('hidden');
        if (dropzone) dropzone.classList.add('border-red-500', 'ring-2', 'ring-red-400');
    }

    function clearInvalid(prefix) {
        var evErr = document.getElementById(partId(prefix, 'error'));
        var dropzone = document.getElementById(partId(prefix, 'dropzone'));
        if (evErr) evErr.classList.add('hidden');
        if (dropzone) dropzone.classList.remove('border-red-500', 'ring-2', 'ring-red-400');
    }

    return {
        init: init,
        reset: reset,
        getData: getData,
        syncHidden: syncHidden,
        markInvalid: markInvalid,
        clearInvalid: clearInvalid,
    };
})();
</script>
@endonce

export function registerPage(Alpine) {
Alpine.data('profilePhotoEditor', () => ({
    previewUrl: null,
    image: null,
    zoom: 1,
    processing: false,
    previewError: false,
    removeExisting: false,
    pendingPhotoChange: false,
    shakePreview: false,
    shakeTimer: null,

    triggerShake() {
        this.shakePreview = false;
        cancelAnimationFrame(this.shakeFrame);
        this.shakeFrame = requestAnimationFrame(() => { this.shakePreview = true; });
        clearTimeout(this.shakeTimer);
        this.shakeTimer = setTimeout(() => { this.shakePreview = false; }, 700);
    },

    select(event) {
        const file = event.target.files?.[0];
        this.previewError = false;
        this.image = null;

        if (this.previewUrl) {
            URL.revokeObjectURL(this.previewUrl);
            this.previewUrl = null;
        }

        if (!file) {
            this.pendingPhotoChange = this.removeExisting;
            return;
        }

        this.removeExisting = false;
        this.pendingPhotoChange = true;
        this.triggerShake();
        this.previewUrl = URL.createObjectURL(file);
        this.zoom = 1;

        const image = new Image();
        image.onload = () => { this.image = image; };
        image.onerror = () => { this.previewError = true; };
        image.src = this.previewUrl;
    },

    clearSelection() {
        if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
        this.previewUrl = null;
        this.image = null;
        this.previewError = false;
        this.$refs.input.value = '';
        this.pendingPhotoChange = this.removeExisting;
        this.shakePreview = false;
    },

    markForRemoval() {
        this.removeExisting = true;
        this.pendingPhotoChange = true;
        this.triggerShake();
    },

    undoRemoval() {
        this.removeExisting = false;
        this.pendingPhotoChange = false;
        this.shakePreview = false;
    },

    destroy() {
        if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
        clearTimeout(this.shakeTimer);
        cancelAnimationFrame(this.shakeFrame);
    },

    submit(event) {
        const form = event.target;

        if (!this.image) {
            form.submit();
            return;
        }

        this.processing = true;
        const canvas = document.createElement('canvas');
        const size = 512;
        const sourceSize = Math.min(this.image.naturalWidth, this.image.naturalHeight) / this.zoom;
        const sourceX = (this.image.naturalWidth - sourceSize) / 2;
        const sourceY = (this.image.naturalHeight - sourceSize) / 2;
        canvas.width = size;
        canvas.height = size;
        canvas.getContext('2d').drawImage(
            this.image,
            sourceX,
            sourceY,
            sourceSize,
            sourceSize,
            0,
            0,
            size,
            size,
        );

        canvas.toBlob((blob) => {
            if (!blob) {
                this.processing = false;
                this.previewError = true;
                return;
            }

            const croppedFile = new File([blob], 'profile-picture.jpg', { type: 'image/jpeg' });
            const transfer = new DataTransfer();
            transfer.items.add(croppedFile);
            this.$refs.input.files = transfer.files;
            form.submit();
        }, 'image/jpeg', 0.9);
    },
}));
}

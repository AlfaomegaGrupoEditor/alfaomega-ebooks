<script setup lang="ts">
    import {ref, watch} from 'vue';
    import {aoButton, aoAccessDetails} from '@/components';
    import {BookType} from '@/types';
    import {useI18n} from 'vue-i18n';

    const props = defineProps({
        show: Boolean,
        data: {type: Object as () => BookType | null, required: true}
    });

    const {t} = useI18n();
    const emit = defineEmits(['update:show']);
    const show = ref(props.show);
    const book = ref<BookType | null>(null);
    const processing = ref(false);
    const covers = ref(window.wpApiSettings.covers);

    const handleClose = () => {
        emit('update:show', !show.value);
    };

    const handleDownload = () => {
        processing.value = true;
        if (book.value?.downloadUrl) {
            window.open(book.value.downloadUrl, '_self');
        }
        processing.value = false;
    };

    const handleReadOnline = () => {
        processing.value = true;
        if (book.value?.readUrl) {
            window.open(book.value.readUrl, '_self');
        }
        processing.value = false;
    };

    watch(() => props.show, (newVal) => {
        show.value = newVal;
    });

    watch(() => props.data, (newVal) => {
        book.value = newVal;
    });
</script>

<template>
    <BOffcanvas
        v-if="book"
        v-model="show"
        class="ao-sidebar"
        placement="end"
        style="z-index: 9999999991"
        :hide-backdrop="false"
        :header="false"
        :shadow="true"
        teleport-to="#ao-container"
        @hide="handleClose"
    >
        <template #title>
            <span class="text-primary mt-2">
                {{ book.title }}
            </span>
        </template>

        <div class="mb-2">
            <span class="fw-bold fs-7">{{ book.categories }}</span>
        </div>

        <div class="mx-4">
            <img
                class="img-thumbnail"
                style="max-height: 400px"
                :src="covers + book.cover"
                :alt="book.title"
                width="310"
            />
        </div>

        <div class="mt-4 d-flex justify-content-center">
            <ao-button
                icon="fa-file-pdf"
                :caption="$t('download')"
                :disabled="!book.download"
                @click="handleDownload"
                :tooltip="$t('download_tooltip')"
                :loading="processing"
            />

            <ao-button
                icon="fa-wifi"
                :caption="$t('read_online')"
                :disabled="!book.read"
                @click="handleReadOnline"
                :tooltip="$t('read_tooltip')"
                :loading="processing"
            />
        </div>

        <ao-access-details
            :type="book.accessType"
            :status="book.status"
            :added_at="book.addedAt"
            :valid_until="book.validUntil"
            :book_url="book.url"
        />
    </BOffcanvas>
</template>

<style scoped>

</style>

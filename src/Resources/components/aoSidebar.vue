<script setup lang="ts">
    import {ref, watch} from 'vue';
    import {aoButton, aoAccessDetails} from '@/components';
    import {BookType} from '@/types';

    const props = defineProps({
        show: Boolean,
        data: {type: Object as () => BookType, required: true}
    });

    const emit = defineEmits(['update:show']);
    const show = ref(props.show);
    const book = ref<BookType | null>(null);

    const handleClose = () => {
        emit('update:show', !show.value);
    };

    const handleDownload = () => {
        if (book.value?.downloadUrl) {
            window.open(book.value.downloadUrl, '_self');
        }
    };

    const handleReadOnline = () => {
        if (book.value?.readUrl) {
            window.open(book.value.readUrl, '_self');
        }
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
                :src="book.cover"
                :alt="book.title"
            />
        </div>

        <div class="mt-4 d-flex justify-content-center">
            <ao-button
                icon="fa-file-pdf"
                :caption="$t('download')"
                :disabled="!book.download"
                @click="handleDownload"
            />

            <ao-button
                icon="fa-wifi"
                :caption="$t('read_online')"
                :disabled="!book.read"
                @click="handleReadOnline"
            />
        </div>

        <ao-access-details
            :type="book.type"
            :status="book.status"
            :added_at="book.addedAt"
            :valid_until="book.validUntil"
            :book_url="book.url"
        />
    </BOffcanvas>
</template>

<style scoped>

</style>

<script setup lang="ts">
import {ref, computed, defineEmits} from 'vue';
    import {aoButton} from '@/components';
import {BookType, ToastType} from '@/types';
    import {useI18n} from 'vue-i18n';
    import {API} from '@/services';
    import {useLibraryStore} from '@/stores';

    const emit = defineEmits<{ (e: 'apply', payload: ToastType): void }>();
    const {t} = useI18n();
    const libraryStore = useLibraryStore();
    const code = ref('');
    const processing = ref(false);
    const invalidCode = computed(() => !/^[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}$/.test(code.value));

    const handleClick = async () => {
        processing.value = true;
        try {
            const response = await API.library.applyCode(code.value);
            if (response.status === 'success') {
                emit('apply', {
                    content: response.message,
                    variant: 'success',
                    title: t('success')
                } as ToastType);
                code.value = '';
                processing.value = false;
                setTimeout(() => window.location.reload(), 1000);
            } else {
                emit('apply', {
                    content: response.message != null
                        ? response.message
                        : t('something_went_wrong'),
                    variant: 'primary',
                    title: t('process_failed')
                } as ToastType);
                code.value = '';
                processing.value = false;
            }
        } catch (e) {
            code.value = '';
            processing.value = false;
        }
    };

</script>

<template>
    <b-card>
        <b-card-title class="mt-0">
            <span class="fs-8">
                {{ $t('add_ebooks') }}
            </span>
        </b-card-title>

        <b-card-text class="fs-8">
            {{ $t('add_sample_text') }}
            {{ $t('purchase_note') }}
        </b-card-text>

        <div class="mb-4">
            <BFormInput
                class="form-control-sm fw-bold text-primary"
                v-model="code"
                :placeholder="t('paste_code_here')"
                type="text"
            />
        </div>

        <div class="row">
            <div class="col">
                <ao-button
                    class="float-end"
                    icon="fa-key"
                    :caption="$t('apply_btn')"
                    :disabled="invalidCode"
                    size="sm"
                    @click="handleClick"
                    :loading="processing"
                />
            </div>
        </div>

        <div class="card-body fs-8 text-muted d-none">
            <span class="fw-bold">{{ $t('note') }}: </span>
            <span>{{ $t('purchase_note') }}</span>
        </div>
    </b-card>
</template>

<style scoped>

</style>

<script setup lang="ts">
  import { useI18n } from "vue-i18n";
  import { ref } from 'vue';

  const { t } = useI18n();

  const accessType = ref(null)
  const accessTypeOptions = [
    {value: null, text: t('access_type')},
    {value: 'purchases', text: t('purchased')},
    {value: 'samples', text: t('samples')},
  ]

  const accessStatus = ref(null)
  const accessStatusOptions = [
    {value: null, text: t('status')},
    {value: 'created', text: t('created')},
    {value: 'active', text: t('active')},
    {value: 'expired', text: t('expired')},
    {value: 'cancelled', text: t('cancelled')},
  ]

  const search = ref(null)
</script>

<template>
  <div class="row">
    <div class="col">
      <div class="row">
        <!--      type-->
        <div class="col">
          <BFormSelect
              is="access-type-select"
              v-model="accessType"
              :options="accessTypeOptions"
              size="sm"
          />
        </div>

        <!--      status-->
        <div class="col">
          <BFormSelect
              is="access-status-select"
              v-model="accessStatus"
              :options="accessStatusOptions"
              size="sm"
          />
        </div>

        <!--      search-->
        <div class="col-5">
          <BInputGroup size="sm">
            <BFormInput
                class="form-control-sm"
                id="search-input"
                :placeholder="$t('search')"
                type="text"
                debounce="500"
            />
            <BInputGroupText>
              <i class="fa fa-search"></i>
            </BInputGroupText>
          </BInputGroup>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>

<script setup lang="ts">
    import {reactive, defineEmits, onMounted, computed, watch} from 'vue';
    import Tree from 'vue3-treeview';
    import 'vue3-treeview/dist/style.css';
    import {useI18n} from 'vue-i18n';
    import {useLibraryStore} from '@/stores';
    import {updateHistory} from '@/services/Helper';
    import {eventBus} from '@/events';
    import {getValue} from '@/services/Helper';

    const emit = defineEmits(['selected']);

    const {t} = useI18n();
    const libraryStore = useLibraryStore();
    const catalog = computed(() => libraryStore.getCatalog);

    const nodes = reactive({
        all_ebooks: {
            text: t('all_ebooks'),
            state: {
                opened: false,
                checked: false
            },
            children: []
        },
        purchased: {
            text: t('purchased')
        },
        samples: {
            text: t('samples')
        }
    });
    const config = {
        roots: ['all_ebooks', 'purchased', 'samples'],
        padding: 15,
        openedIcon: {
            type: 'class',
            class: 'fas fa-angle-down'
        },
        closedIcon: {
            type: 'class',
            class: 'fas fa-angle-right',
            height: '30px',
            width: '30px'
        }
    };

    /**
     * Opens the node and its parent nodes recursively.
     * @param {string} id
     */
    const openNode = (id) => {
        const parent = Object.keys(nodes).findIndex((key) => {
            return nodes[key].children && nodes[key].children.includes(Number(id));
        });
        if (parent != -1) {
            const parentId = Object.keys(nodes)[parent];
            openNode(parentId);
        }
        nodes[id].state.opened = true;
    };

    /**
     * Get the descendants of a node
     * @param id
     */
    const nodeDescendants = (id): string => {
        let descendants = '';
        const traverse = (node) => {
            descendants += descendants === '' ? String(node.id) : `,${node.id}`;
            if (node.children && node.children.length > 0) {
                node.children.forEach((child) => {
                    traverse(nodes[child]);
                });
            }
        };

        traverse(nodes[id]);
        return descendants;
    };

    const handleClick = (node) => {
        // TODO: filter by accessType on purchased and samples
        let categories = null;
        if (node.id !== 'all_ebooks'
            && node.id !== 'purchased'
            && node.id !== 'samples') {
            categories = nodeDescendants(node.id);
            eventBus.emit('catalogSelected', 'all_ebooks');
        } else {
            eventBus.emit('catalogSelected', node.id);
        }

        updateHistory(null, node.id);
        emit('selected', {categories: categories, text: node.text, id: node.id});
    };

    const handleBlur = (node) => {
        //setClass('.tree .focused', 'focused', false);
    };

    const traverse = (node) => {
        category += category === '' ? node.id : `, ${node.id}`;
        if (node.children) {
            node.children.forEach((child) => {
                traverse(child);
            });
        }
    };

    onMounted(() => {
        libraryStore.dispatchLoadCatalog();
    });

    watch(catalog, (newVal) => {
        const urlParams = new URLSearchParams(window.location.search);
        const category = getValue(urlParams.get('category'), 'all_ebooks');
        nodes['all_ebooks']['children'] = newVal.root;
        nodes['all_ebooks']['state']['opened'] = true;

        Object.keys(newVal.tree).forEach((key) => {
            nodes[key] = {
                id: key,
                text: newVal.tree[key].title,
                children: newVal.tree[key].children,
                state: {
                    opened: false,
                    checked: false
                }
            };
        });

        openNode(category);
        const categories = category === 'all_ebooks' ? null : nodeDescendants(category);
        emit('selected', {categories: categories, text: nodes[category].text, id: category});
    });

</script>

<template>
    <div class="mb-2">
        <h4 class="fs-6">{{ $t('digital_library') }}</h4>
        <Tree
            :nodes="nodes"
            :config="config"
            @nodeFocus="handleClick"
            @nodeBlur="handleBlur"
        />
    </div>
</template>

<style scoped>

</style>

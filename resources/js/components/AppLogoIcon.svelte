<script lang="ts">
    import stokkerIcon from '../../images/favicon.png';

    let {
        alt = 'Stokker',
        class: className = '',
        ...rest
    }: {
        alt?: string;
        class?: string;
        [key: string]: unknown;
    } = $props();
</script>

<img src={stokkerIcon} {alt} class={className} {...rest} />

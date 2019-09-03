(function (plugins, editPost, element, components, data, compose, blocks) {

    const el = element.createElement;

    const {Fragment} = element;
    const {registerPlugin} = plugins;
    const {PluginSidebar, PluginSidebarMoreMenuItem} = editPost;
    const {PanelBody, SelectControl} = components;
    const {withSelect, withDispatch, dispatch} = data;
    const {parse} = blocks;

    var pluginIcon = 'media-spreadsheet';

    const PostsDropdownControl = compose.compose(
        // withDispatch allows to save the selected post ID into post meta
        withDispatch(function (dispatch, props) {
            return {
                setMetaValue: function (metaValue) {
                    dispatch('core/editor').editPost(
                        {meta: {[props.metaKey]: metaValue}}
                    );
                }
            }
        }),
        // withSelect allows to get posts for our SelectControl and also to get the post meta value
        withSelect(function (select, props) {
            return {
                posts: select('core').getEntityRecords('postType', 'gb-template'),
                metaValue: select('core/editor').getEditedPostAttribute('meta')[props.metaKey],
            }
        }))(function (props) {

            // options for SelectControl
            var options = [];

            // if posts found
            if (props.posts) {
                options.push({value: 0, label: 'Select a page layout'});
                props.posts.forEach((post) => { // simple foreach loop
                    console.log(post);
                    options.push({value: post.content.raw, label: post.title.rendered});
                });
            } else {
                options.push({value: 0, label: 'Loading...'})
            }

            return el(SelectControl,
                {
                    label: 'Select a post',
                    options: options,
                    onChange: function (content) {
                        props.setMetaValue(content);

                        const {resetBlocks} = dispatch('core/block-editor');
                        resetBlocks(parse(content));
                    },
                    value: props.metaValue,
                }
            );

        }
    );

    registerPlugin('sschat-pagetemplates', {
        render: function () {
            return el(Fragment, {},
                el(PluginSidebarMoreMenuItem,
                    {
                        target: 'sschat-pagetemplates',
                        icon: pluginIcon,
                    },
                    'SEO'
                ),
                el(PluginSidebar,
                    {
                        name: 'sschat-pagetemplates',
                        icon: pluginIcon,
                        title: 'Page template',
                    },
                    el(PanelBody, {},
                        // Field 1
                        el(PostsDropdownControl,
                            {
                                metaKey: 'sschat_page_layout',
                                title: 'Page layout',
                            }
                        )
                    )
                )
            );
        }
    });

})(
    window.wp.plugins,
    window.wp.editPost,
    window.wp.element,
    window.wp.components,
    window.wp.data,
    window.wp.compose,
    window.wp.blocks
);
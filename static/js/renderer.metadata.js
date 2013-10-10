/**
 * Metadata is an application for Novius OS for adding metadata on models.
 *
 * @copyright  2013 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link https://github.com/novius/novius_metadata
 */
define(
    ['jquery-nos'],
    function($) {
    "use strict";
    var undefined = void(0);
    $.widget( "novius.renderermetadata", {
        options: {
            input: '',
            single: false,
            url_picker: '',
            texts : {
                add : 'Add items',
                choose : 'Choose an item',
                remove : 'Remove item',
                edit : 'Edit item'
            }
        },

        ids: {},

        _create: function() {
            var self = this,
                o = self.options;

            self.$container = this.element.addClass('novius_metadata-renderer');

            if (o.single) {
                self.$input = self.$container.find(':input:first');

                if (self.$input.val()) {
                    self._uiSingleEdit();
                } else {
                    self._uiSingleChoose();
                }
            } else {
                o.input = o.input + '[]';

                self.$container.find(':input').each(function() {
                    var $hidden = $(this);
                    self.ids[$hidden.val()] = $hidden.val();

                    self._uiTag($hidden)
                        .appendTo(self.$container);
                });

                self.$add = $.nosUIElement({
                    type: 'button',
                    label: o.texts.add,
                    icon: 'plus',
                    bind: {
                        click: function() {
                            var $dialog = self.$add.nosDialog({
                                ajax: true,
                                ajaxData: {
                                    context: self.$container.closest('.nos-dispatcher, body').data('nosContext')
                                },
                                contentUrl: o.url_picker,
                                title: o.texts.add
                            });
                            $dialog.on('select_nature', function(e, item) {
                                if (!self.ids[item._id]) {
                                    var $hidden = $('<input type="hidden" />').attr('name', o.input)
                                        .val(item._id)
                                        .prependTo(self.$container)
                                        .data('title', item._title);
                                    self._uiTag($hidden).insertBefore(self.$add);
                                    self.$container.nosFormUI();
                                }
                                $dialog.nosDialog('close');
                            });
                        }
                    }
                }).appendTo(self.$container);
            }
        },

        _init: function() {
            var self = this,
                o = self.options;

            self.$container.nosFormUI();
        },

        _uiTag : function($hidden) {
            var self = this,
                o = self.options;

            var $button = $.nosUIElement({
                    type: 'button',
                    label: $hidden.data('title'),
                    icons: {
                        secondary: 'close'
                    },
                    bind: {
                        click: function() {
                            delete self.ids[$hidden.val()];
                            $button.remove();
                            $hidden.remove();
                        }
                    }
                })
                .addClass('novius_metadata-renderer-tag');
            return $button;
        },

        _uiSingleInit : function() {
            var self = this;

            self.$choose && self.$choose.remove();
            self.$title && self.$title.remove();
            self.$edit && self.$edit.remove();
            self.$remove && self.$remove.remove();
        },

        _uiSingleChoose : function() {
            var self = this,
                o = self.options;

            self._uiSingleInit();

            self.$choose = $.nosUIElement({
                type: 'button',
                label: o.texts.choose,
                icon: 'plus',
                bind: {
                    click: function() {
                        var $dialog = self.$choose.nosDialog({
                            ajax: true,
                            ajaxData: {
                                context: self.$container.closest('.nos-dispatcher, body').data('nosContext')
                            },
                            contentUrl: o.url_picker,
                            title: o.texts.choose
                        });
                        $dialog.on('select_nature', function(e, item) {
                            self.$input.val(item._id).data('title', item._title);
                            self._uiSingleEdit();
                            $dialog.nosDialog('close');
                        });
                    }
                }
            }).appendTo(self.$container);

            self.$container.nosFormUI();

            return self;
        },

        _uiSingleEdit : function() {
            var self = this,
                o = self.options;

            self._uiSingleInit();

            self.$title = $('<span></span>').text(self.$input.data('title'))
                .addClass('novius_metadata-title')
                .appendTo(self.$container);

            self.$edit = $.nosUIElement({
                type: 'button',
                label: o.texts.edit,
                icon: 'pencil',
                text: false,
                bind: {
                    click: function() {
                        var $dialog = self.$edit.nosDialog({
                            ajax: true,
                            ajaxData: {
                                context: self.$container.closest('.nos-dispatcher, body').data('nosContext')
                            },
                            contentUrl: o.url_picker,
                            title: o.texts.edit
                        });
                        $dialog.on('select_nature', function(e, item) {
                            self.$input.val(item._id).data('title', item._title);
                            self._uiSingleEdit();
                            $dialog.nosDialog('close');
                        });
                    }
                }
            }).appendTo(self.$container);

            self.$remove = $.nosUIElement({
                type: 'button',
                label: o.texts.remove,
                icon: 'close',
                text: false,
                bind: {
                    click: function() {
                        self.$input.val('').data('title', '');
                        self._uiSingleChoose();
                        $dialog.nosDialog('close');
                    }
                }
            }).appendTo(self.$container);

            self.$container.nosFormUI();

            return self;
        }
    });
    return $;
});




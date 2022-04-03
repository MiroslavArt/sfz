BX.namespace('iTrack.Crm.BpExt');

BX.iTrack.Crm.BpExt = {
    entityId: null,
    entityTypeId: null,
    entityModel: null,
    timelineInstance: null,
    tasks: [],
    init: function () {
        if(typeof (BX.Crm.EntityEditor) !== 'undefined') {
            console.log("firstcond")
            var editor = BX.Crm.EntityEditor.getDefault();
            if(editor) {
                this.detailHandler(editor, {
                    id: editor._id,
                    externalContext: editor._externalContextId,
                    context: editor._contextId,
                    entityTypeId: editor._entityTypeId,
                    entityId: editor._entityId,
                    model: editor._model
                });
            } else {
                console.log("secondcond")
                BX.addCustomEvent('BX.Crm.EntityEditor:onInit', BX.delegate(this.detailHandler, this));
            }
        } else {
            console.log("thirdcond")
            BX.addCustomEvent('BX.Crm.EntityEditor:onInit', BX.delegate(this.detailHandler, this));
        }
    },
    detailHandler: function (editor, data) {
        console.log(editor)
        console.log(data)
        if(data.hasOwnProperty('entityId') && data.hasOwnProperty('model')) {
            console.log("inside")
            this.entityId = data.entityId;
            this.entityTypeId = data.model.getEntityTypeId();
            if(data.hasOwnProperty('model')) {
                this.model = data.model;
            }
            console.log(this.entityId)
            console.log(this.entityTypeId)
            this.getTasks();
            
            BX.addCustomEvent("onPullEvent-crm", BX.delegate(this.onPullEvent, this));
        }
    },
    getTasks: function() {
        BX.ajax.runAction('itrack:bpextension.api.bptask.list', {
            data: {
                entityType: this.entityTypeId,
                entityId: this.entityId
            }
        }).then(function (response) {
            if(response.hasOwnProperty('status') && response.hasOwnProperty('data')) {
                if(response.status === 'success') {
                    this.processBpTasks(response.data);
                }
            }
        }.bind(this));
    },
    onPullEvent: function(command, data) {
        if(command === 'timeline_bizproc_status') {
            if(data.hasOwnProperty('TAG') && data.hasOwnProperty('HISTORY_ITEM')) {
                if(data.TAG === 'CRM_TIMELINE_' + BX.CrmEntityType.resolveName(this.entityTypeId) + '_' + this.entityId) {
                    if(data.HISTORY_ITEM.WORKFLOW_STATUS_NAME === 'Created'
                        || data.HISTORY_ITEM.WORKFLOW_STATUS_NAME === 'Completed') {
                        this.getTasks();
                    }
                }
            }
        }
        if(command === 'crm_bizproc_task_create') {
            if(data.hasOwnProperty('TAG')) {
                if(data.TAG === 'CRM_BP_TASK_' + BX.CrmEntityType.resolveName(this.entityTypeId) + '_' + this.entityId) {
                    this.getTasks();
                }
            }
        }
    },
    processBpTasks: function (responseData) {
        var timelineInstanceName;
        switch(this.entityTypeId) {
            case 1:
                timelineInstanceName = 'lead';
                break;
            case 2:
                timelineInstanceName = 'deal';
                break;
            case 3:
                timelineInstanceName = 'contact';
                break;
            case 4:
                timelineInstanceName = 'company';
                break;
        }
        timelineInstanceName += '_' + this.entityId + '_details_timeline';
        console.log(timelineInstanceName)
        this.timelineInstance = BX.CrmTimelineManager.instances[timelineInstanceName];
        console.log(this.timelineInstance)
        for(var i in this.tasks) {
            this.tasks[i]._schedule.deleteItem(this.tasks[i]);
        }
        this.tasks.splice(0, this.tasks.length);

        for(var i in responseData) {
            var taskItem = responseData[i];
            var newItem = BX.iTrack.Crm.CrmScheduleItemBP.create(
                'BP_TASK_' + taskItem.ID,
                {
                    schedule: this.timelineInstance._schedule,
                    container: this.timelineInstance._schedule._wrapper,
                    activityEditor: this.timelineInstance._schedule._activityEditor,
                    data: {
                        'ASSOCIATED_ENTITY_TYPE_ID': 'BP_TASK_ACT',
                        'ASSOCIATED_ENTITY_ID': 'BP_TASK_' + taskItem.ID,
                        'ASSOCIATED_ENTITY': {
                            'ID': 'BP_TASK_' + taskItem.ID,
                            'OWNER_ID': this.entityId,
                            'OWNER_TYPE_ID': this.entityTypeId,
                            'TYPE_ID': 'BP_TASK_ACT',
                            'PROVIDER_ID': 'CRM_REQUEST',
                            'PROVIDER_TYPE_ID': 'REQUEST',
                            'ASSOCIATED_ENTITY_ID': taskItem.ID,
                            'DIRECTION': '0',
                            'SUBJECT': taskItem.NAME,
                            'STATUS': '1',
                            'DESCRIPTION_TYPE': '1',
                            //'RESPONSIBLE_ID': '496',
                            'DESCRIPTION_RAW': taskItem.DESCRIPTION,
                            //'PERMISSIONS': {'USER_ID': '496', 'POSTPONE': true, 'COMPLETE': true},
                            'START_DATE': taskItem.MODIFIED,
                            'WORKFLOW_TEMPLATE_NAME': taskItem.WORKFLOW_TEMPLATE_NAME,
                            'WORKFLOW_ID': taskItem.WORKFLOW_ID,
                            'DOCUMENT_ID': taskItem.DOCUMENT_ID,
                            'TASK_ID': taskItem.ID
                        },
                        //'AUTHOR_ID': '496',
                        //'AUTHOR': {'FORMATTED_NAME': 'ИНТЕГРАТОР БИТРИКС24', 'SHOW_URL': '/company/personal/user/496/'}
                    }
                }
            );

            var index = this.timelineInstance._schedule.calculateItemIndex(newItem);
            var anchor = this.timelineInstance._schedule.createAnchor(index);
            this.timelineInstance._schedule.addItem(newItem, index);
            newItem.layout({ anchor: anchor });
            this.tasks.push(newItem);
        }
    }
};

BX.ready(function() {
    if (typeof (BX.iTrack.Crm.CrmScheduleItemBP) === "undefined" && typeof (BX.CrmScheduleItem) !== 'undefined') {
        BX.iTrack.Crm.CrmScheduleItemBP = function () {
            BX.iTrack.Crm.CrmScheduleItemBP.superclass.constructor.apply(this);
            this._postponeController = null;
        };
        BX.extend(BX.iTrack.Crm.CrmScheduleItemBP, BX.CrmScheduleItem);
        BX.iTrack.Crm.CrmScheduleItemBP.prototype.getTypeId = function () {
            return 'BP_TASK_ACT';
        };
        BX.iTrack.Crm.CrmScheduleItemBP.prototype.getDeadline = function () {
            var entityData = this.getAssociatedEntityData();
            var time = BX.parseDate(
                entityData["DEADLINE_SERVER"],
                false,
                "YYYY-MM-DD",
                "YYYY-MM-DD HH:MI:SS"
            );

            if (!time) {
                return null;
            }

            return new Date(time.getTime() + 1000 * BX.CrmTimelineItem.getUserTimezoneOffset());
        };
        BX.iTrack.Crm.CrmScheduleItemBP.prototype.getStartDate = function () {
            var entityData = this.getAssociatedEntityData();
            var time = BX.parseDate(
                entityData["START_DATE"],
                false,
                "DD-MM-YYYY",
                "DD-MM-YYYY HH:MI:SS"
            );

            if (!time) {
                return null;
            }

            return new Date(time.getTime() + 1000 * BX.CrmTimelineItem.getUserTimezoneOffset());
        };
        BX.iTrack.Crm.CrmScheduleItemBP.prototype.markAsDone = function (isDone) {
            isDone = !!isDone;
            this.getAssociatedEntityData()["STATUS"] = isDone ? BX.CrmActivityStatus.completed : BX.CrmActivityStatus.waiting;
        };
        BX.iTrack.Crm.CrmScheduleItemBP.prototype.getPrepositionText = function (direction) {
            return this.getMessage(direction === BX.CrmActivityDirection.incoming ? "from" : "to");
        };
        BX.iTrack.Crm.CrmScheduleItemBP.prototype.getTypeDescription = function (direction) {
            return "";
        };
        BX.iTrack.Crm.CrmScheduleItemBP.prototype.isContextMenuEnabled = function () {
            return false;
        };
        BX.iTrack.Crm.CrmScheduleItemBP.prototype.getIconClassName = function () {
            return 'crm-entity-stream-section-icon crm-entity-stream-section-icon-bp';
        };
        BX.iTrack.Crm.CrmScheduleItemBP.prototype.prepareContent = function (options) {
            var startDate = this.getStartDate();
            var timeText = startDate ? this.formatDateTime(startDate) : this.getMessage("termless");

            var entityData = this.getAssociatedEntityData();
            //var direction = BX.prop.getInteger(entityData, "DIRECTION", 0);
            //var isDone = this.isDone();
            var subject = BX.prop.getString(entityData, "SUBJECT", "");
            var description = BX.prop.getString(entityData, "DESCRIPTION_RAW", "");

            //var communication = BX.prop.getObject(entityData, "COMMUNICATION", {});
            //var title = BX.prop.getString(communication, "TITLE", "");
            //var showUrl = BX.prop.getString(communication, "SHOW_URL", "");
            //var communicationValue = BX.prop.getString(communication, "TYPE", "") !== ""
            //    ? BX.prop.getString(communication, "VALUE", "") : "";

            var wrapperClassName = this.getWrapperClassName();
            if (wrapperClassName !== "") {
                wrapperClassName = "crm-entity-stream-section crm-entity-stream-section-planned" + " " + wrapperClassName;
            } else {
                wrapperClassName = "crm-entity-stream-section crm-entity-stream-section-planned";
            }

            var wrapper = BX.create("DIV", {attrs: {className: wrapperClassName}});

            var iconClassName = this.getIconClassName();
            if (this.isCounterEnabled()) {
                iconClassName += " crm-entity-stream-section-counter";
            }
            wrapper.appendChild(BX.create("DIV", {attrs: {className: iconClassName}}));

            //region Context Menu
            if (this.isContextMenuEnabled()) {
                wrapper.appendChild(this.prepareContextMenuButton());
            }
            //endregion

            var contentWrapper = BX.create("DIV",
                {attrs: {className: "crm-entity-stream-section-content"}}
            );
            wrapper.appendChild(contentWrapper);

            //region Details
            if (description !== "") {
                //trim leading spaces
                description = description.replace(/^\s+/, '');
            }

            var contentInnerWrapper = BX.create("DIV",
                {
                    attrs: {className: "crm-entity-stream-content-event"}
                }
            );
            contentWrapper.appendChild(contentInnerWrapper);

            this._deadlineNode = BX.create("SPAN",
                {attrs: {className: "crm-entity-stream-content-event-time"}, text: timeText}
            );

            var headerWrapper = BX.create("DIV",
                {
                    attrs: {className: "crm-entity-stream-content-header"},
                    children:
                        [
                            BX.create("SPAN",
                                {
                                    attrs:
                                        {
                                            className: "crm-entity-stream-content-event-title"
                                        },
                                    text: 'Задание бизнес-процесса ' + BX.prop.getString(entityData, "WORKFLOW_TEMPLATE_NAME", "")
                                }
                            ),
                            this._deadlineNode
                        ]
                }
            );
            contentInnerWrapper.appendChild(headerWrapper);

            var detailWrapper = BX.create("DIV",
                {
                    attrs: {className: "crm-entity-stream-content-detail"}
                }
            );
            contentInnerWrapper.appendChild(detailWrapper);

            detailWrapper.appendChild(
                BX.create("DIV",
                    {
                        attrs: {className: "crm-entity-stream-content-detail-title"},
                        children:
                            [
                                BX.create("A",
                                    {
                                        attrs: {href: "#"},
                                        events: {"click": this._headerClickHandler},
                                        text: subject
                                    }
                                )
                            ]
                    }
                )
            );

            detailWrapper.appendChild(
                BX.create("DIV",
                    {
                        attrs: {className: "crm-entity-stream-content-detail-description"},
                        text: this.cutOffText(description, 128)
                    }
                )
            );

            detailWrapper.appendChild(
                BX.create("DIV",
                    {
                        attrs: {className: "crm-entity-stream-content-detail-button"},
                        children:
                            [
                                BX.create("button",
                                    {
                                        attrs: {className:'ui-btn ui-btn-primary'},
                                        events: {"click": BX.delegate(this.openTask, this)},
                                        text: 'Приступить к выполнению'
                                    }
                                )
                            ]
                    }
                )
            );

            var additionalDetails = this.prepareDetailNodes();
            if (BX.type.isArray(additionalDetails)) {
                for (var i = 0, length = additionalDetails.length; i < length; i++) {
                    detailWrapper.appendChild(additionalDetails[i]);
                }
            }

            /*var members = BX.create("DIV",
                {attrs: {className: "crm-entity-stream-content-detail-contact-info"}}
            );

            if (title !== '') {
                members.appendChild(
                    BX.create("SPAN",
                        {text: this.getPrepositionText(direction) + ": "}
                    )
                );

                if (showUrl !== '') {
                    members.appendChild(
                        BX.create("A",
                            {
                                attrs: {href: showUrl},
                                text: title
                            }
                        )
                    );
                } else {
                    members.appendChild(BX.create("SPAN", {text: title}));
                }
            }

            if (communicationValue !== '') {
                var communicationNode = this.prepareCommunicationNode(communicationValue);
                if (communicationNode) {
                    members.appendChild(communicationNode);
                }
            }

            detailWrapper.appendChild(members);*/
            //endregion
            //region Set as Done Button
            /*var setAsDoneButton = BX.create("INPUT",
                {
                    attrs:
                        {
                            type: "checkbox",
                            className: "crm-entity-stream-planned-apply-btn",
                            checked: isDone
                        },
                    events: { change: this._setAsDoneButtonHandler }
                }
            );

            if(!this.canComplete())
            {
                setAsDoneButton.disabled = true;
            }

            var buttonContainer = BX.create("DIV",
                {
                    attrs: { className: "crm-entity-stream-content-detail-planned-action" },
                    children: [ setAsDoneButton ]
                }
            );
            contentInnerWrapper.appendChild(buttonContainer);*/
            //endregion

            //region Author
            /*var authorNode = this.prepareAuthorLayout();
            if (authorNode) {
                contentInnerWrapper.appendChild(authorNode);
            }*/
            //endregion

            //region  Actions
            /*this._actionContainer = BX.create("DIV",
                {
                    attrs: {className: "crm-entity-stream-content-detail-action"}
                }
            );
            contentInnerWrapper.appendChild(this._actionContainer);*/
            //endregion

            return wrapper;
        };
        BX.iTrack.Crm.CrmScheduleItemBP.prototype.openTask = function(event) {
            var entityData = this.getAssociatedEntityData();
            BX.Bizproc.showTaskPopup(entityData['TASK_ID'], this.taskCallback.bind(this));
        };
        BX.iTrack.Crm.CrmScheduleItemBP.prototype.taskCallback = function(event, param) {
            this._schedule.deleteItem(this);
        };
        BX.iTrack.Crm.CrmScheduleItemBP.prototype.prepareCommunicationNode = function (communicationValue) {
            return BX.create("SPAN", {text: " " + communicationValue});
        };
        BX.iTrack.Crm.CrmScheduleItemBP.prototype.prepareDetailNodes = function () {
            return [];
        };
        BX.iTrack.Crm.CrmScheduleItemBP.create = function(id, settings)
        {
            var self = new BX.iTrack.Crm.CrmScheduleItemBP();
            self.initialize(id, settings);
            return self;
        };
    }
});
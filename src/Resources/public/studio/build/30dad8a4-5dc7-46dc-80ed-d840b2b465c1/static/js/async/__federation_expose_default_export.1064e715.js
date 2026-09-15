/*! For license information please see __federation_expose_default_export.1064e715.js.LICENSE.txt */
"use strict";(self["chunk_pimcore_datahub_bundle "]=self["chunk_pimcore_datahub_bundle "]||[]).push([["525"],{4728(e,l,i){i.r(l),i.d(l,{BaseDetailView:()=>d.Wc,ColumnLocaleControl:()=>W,StatePill:()=>x,stringifyYaml:()=>S,ADVANCED_COLUMN_KEY:()=>N,useCompactLayout:()=>I,advancedToSchemaColumn:()=>L,ADVANCED_COLUMN_TYPE:()=>A,BaseColumnEditor:()=>J,CompactLayoutProvider:()=>k,ColumnEditorItemBody:()=>R,DynamicTypeDataHubAdapterAbstract:()=>r.E,DynamicTypeDataHubAdapterRegistry:()=>s.f,DataHubLogTab:()=>ea,PermissionsTab:()=>a.K,ConfigToolbar:()=>d.MM,advancedFromSchemaColumn:()=>z,GeneralTab:()=>t.a,ColumnConfigModal:()=>D,ProposalCard:()=>h,parseYaml:()=>C,trackConfigError:()=>d.Mv,useAddColumnGroups:()=>E,StatusTag:()=>b,ColumnPreview:()=>H,ConfigContainer:()=>n.y,isValidYaml:()=>T,bundleServiceIds:()=>o.s,useDetailView:()=>d.iO,MigrationModal:()=>$,ColumnPipelineForm:()=>X});var n=i(6100),t=i(970),a=i(6427),o=i(17),r=i(5667),s=i(8576),d=i(2379),c=i(4848),u=i(2812),p=i.n(u),m=i(5840);let g=(0,m.rU)(e=>{let{token:l,css:i}=e;return{card:i`
    min-width: 0;
  `,head:i`
    display: flex;
    flex-direction: column;
    gap: ${l.marginXXS}px;
    padding: ${l.paddingXS}px 0 ${l.paddingSM}px;
    border-bottom: 1px solid ${l.colorSplit};
  `,identity:i`
    display: flex;
    align-items: center;
    gap: ${l.marginXS}px;
  `,name:i`
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: ${l.fontSizeLG}px;
    font-weight: ${l.fontWeightStrong};
    color: ${l.colorText};
  `,pill:i`
    flex: none;
    display: inline-flex;
    align-items: center;
    gap: ${l.marginXXS}px;
    height: ${l.controlHeightSM-2}px;
    padding: 0 ${l.paddingXS}px;
    border-radius: ${l.borderRadiusSM}px;
    background: ${l.colorFillTertiary};
    color: ${l.colorTextSecondary};
    font-size: ${l.fontSize}px;
    line-height: 1;
  `,dot:i`
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: ${l.colorTextQuaternary};
  `,dotOn:i`
    background: ${l.colorSuccess};
  `,description:i`
    font-size: ${l.fontSize}px;
    line-height: ${l.lineHeightSM};
    color: ${l.colorTextSecondary};
    overflow-wrap: anywhere;
  `,spine:i`
    display: grid;
    grid-template-columns: ${l.marginXS}px minmax(0, 1fr);
    column-gap: ${l.marginSM}px;
    padding: ${l.padding}px 0;
  `,mark:i`
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: ${l.marginXXS}px;
  `,node:i`
    flex: none;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: ${l.colorPrimary};
  `,nodeMuted:i`
    background: ${l.colorBgContainer};
    box-shadow: inset 0 0 0 1px ${l.colorPrimaryBorder};
  `,line:i`
    flex: 1;
    width: 1px;
    margin: ${l.marginXXS}px 0;
    background: ${l.colorPrimaryBorder};
  `,entry:i`
    min-width: 0;
    padding-bottom: ${l.padding}px;
  `,entryLast:i`
    min-width: 0;
  `,role:i`
    font-size: ${l.fontSizeSM}px;
    font-weight: ${l.fontWeightStrong};
    letter-spacing: .07em;
    text-transform: uppercase;
    color: ${l.colorTextTertiary};
    line-height: ${l.lineHeightSM};
  `,section:i`
    display: inline-flex;
    align-items: center;
    gap: ${l.marginXXS}px;
    max-width: 100%;
    margin: 0 0 ${l.marginXXS}px;
    padding: 0;
    border: none;
    background: none;
    font: inherit;
    font-size: ${l.fontSizeSM}px;
    font-weight: ${l.fontWeightStrong};
    letter-spacing: .07em;
    text-transform: uppercase;
    line-height: ${l.lineHeightSM};
    color: ${l.colorPrimary};
    cursor: pointer;

    &:hover {
      color: ${l.colorPrimaryHover};
    }
  `,value:i`
    font-weight: ${l.fontWeightStrong};
    color: ${l.colorText};
    font-variant-numeric: tabular-nums;
    overflow-wrap: anywhere;
  `,note:i`
    font-size: ${l.fontSizeSM}px;
    line-height: ${l.lineHeightSM};
    color: ${l.colorTextTertiary};
    overflow-wrap: anywhere;
  `,field:i`
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: ${l.marginXS}px;
    min-height: ${l.controlHeightSM}px;

    /* the marks are tall for their row; without this they read as one block */
    & + & {
      margin-top: ${l.marginXXS}px;
    }
  `,fieldLabel:i`
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-weight: ${l.fontWeightStrong};
    color: ${l.colorText};
  `,foot:i`
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding: ${l.paddingSM}px 0;
    border-top: 1px solid ${l.colorSplit};
    font-variant-numeric: tabular-nums;
  `,footLead:i`
    font-size: ${l.fontSize}px;
    line-height: ${l.lineHeightSM};
    color: ${l.colorTextSecondary};

    b {
      color: ${l.colorText};
    }
  `,footGroups:i`
    font-size: ${l.fontSizeSM}px;
    line-height: ${l.lineHeightSM};
    color: ${l.colorTextTertiary};
  `}}),h=e=>{let{name:l,description:i,tags:n,nodes:t,footer:a}=e,{styles:o,cx:r}=g();return(0,c.jsxs)("div",{className:o.card,children:[(0,c.jsxs)("div",{className:o.head,children:[(0,c.jsxs)("div",{className:o.identity,children:[(0,c.jsx)("span",{className:o.name,children:l}),n]}),void 0!==i&&(0,c.jsx)("div",{className:o.description,children:i})]}),(0,c.jsx)("div",{className:o.spine,children:t.map((e,l)=>(0,c.jsxs)(p().Fragment,{children:[(0,c.jsxs)("div",{className:o.mark,children:[(0,c.jsx)("span",{className:r(o.node,!0===e.muted&&o.nodeMuted)}),l<t.length-1&&(0,c.jsx)("span",{className:o.line})]}),(0,c.jsx)("div",{className:l===t.length-1?o.entryLast:o.entry,children:e.body})]},e.key))}),a]})},x=e=>{let{active:l,label:i}=e,{styles:n,cx:t}=g();return(0,c.jsxs)("span",{className:n.pill,children:[(0,c.jsx)("span",{className:t(n.dot,l&&n.dotOn)}),i]})};var f=i(2696),y=i(4781);let v={added:"green",changed:"gold",removed:"red",moved:"geekblue"},b=e=>{let{status:l,translationPrefix:i="data-hub.review.status"}=e,{t:n}=(0,y.useTranslation)();return(0,c.jsx)(f.Tag,{color:v[l],"data-review-mark":"",style:{marginInlineEnd:0},children:n(`${i}.${l}`)})};var j=i(5308);let C=e=>(0,j.parse)(e),S=(e,l)=>(0,j.stringify)(e,{indent:2,...l}),T=e=>{try{return(0,j.parse)(e),!0}catch{return!1}},w=(0,u.createContext)({compact:!1}),k=e=>{let{children:l,compact:i=!1}=e;return(0,u.useMemo)(()=>(0,c.jsx)(w.Provider,{value:{compact:i},children:l}),[l,i])},I=()=>(0,u.useContext)(w),$=e=>{let{open:l,title:i,iconName:n="settings",legacyConfig:t,renderToolbarLeft:a,onConfirm:o,onClose:r,children:s}=e,{t:d}=(0,y.useTranslation)(),[p,m]=(0,u.useState)(!1),g=S(t),h=(0,c.jsx)(f.ModalTitle,{iconName:n,children:i});return p?(0,c.jsxs)(f.Modal,{footer:null,onCancel:()=>{m(!1)},open:l,size:"XL",title:h,children:[(0,c.jsxs)(f.Flex,{gap:"small",style:{height:"calc(80vh - 120px)",overflow:"hidden"},children:[(0,c.jsx)(f.Flex,{style:{flex:1,minWidth:0,overflow:"hidden"},vertical:!0,children:(0,c.jsx)(k,{compact:!0,children:s})}),(0,c.jsx)("div",{style:{width:1,background:"var(--ant-color-split, rgba(0,0,0,.06))",flexShrink:0}}),(0,c.jsxs)(f.Flex,{gap:"small",style:{flex:1,minWidth:0,overflow:"auto"},vertical:!0,children:[(0,c.jsx)(f.Alert,{description:d("data-hub.migration-modal.legacy-notice"),showIcon:!0,type:"warning"}),(0,c.jsx)(f.CodeEditor,{height:"100%",preset:"yaml",readOnly:!0,value:g})]})]}),(0,c.jsxs)(f.Toolbar,{padding:{x:"none",y:"small"},theme:"secondary",children:[a,(0,c.jsxs)(f.Space,{size:"extra-small",children:[(0,c.jsx)(f.Button,{onClick:()=>{m(!1)},type:"default",children:d("data-hub.migration-modal.cancel")}),(0,c.jsx)(f.Button,{onClick:o,type:"primary",children:d("data-hub.migration-modal.confirm-migration")})]})]})]}):(0,c.jsxs)(f.Modal,{footer:null,onCancel:r,open:l,size:"XL",title:h,children:[(0,c.jsxs)(f.Flex,{gap:"small",vertical:!0,children:[(0,c.jsx)(f.Alert,{description:d("data-hub.migration-modal.legacy-notice"),showIcon:!0,type:"warning"}),(0,c.jsx)(f.CodeEditor,{height:"400px",preset:"yaml",readOnly:!0,value:g})]}),(0,c.jsx)(f.Toolbar,{padding:{x:"none",y:"small"},theme:"secondary",children:(0,c.jsx)(f.Space,{size:"extra-small",children:(0,c.jsx)(f.Button,{onClick:()=>{m(!0)},type:"primary",children:d("data-hub.migration-modal.start-migration")})})})]})};var F=i(1161),M=i(3090),P=i(2543);let N="advanced",A="dataobject.advanced",z=e=>{var l,i;return{_id:crypto.randomUUID(),key:e.key,fieldtype:e.fieldtype,type:e.type,config:e.config,pipeline:e.type===A?{title:e.key!==N?e.key:e.title,sourceFields:((null==(l=e.config)?void 0:l.advancedColumns)??[]).map(e=>({...e,config:e.config??{}})),transformers:null==(i=e.config)?void 0:i.transformers}:void 0,locale:e.locale}},L=e=>{var l;return{key:e.type===A&&(null==(l=e.pipeline)?void 0:l.title)!==void 0&&""!==e.pipeline.title?e.pipeline.title:e.key,fieldtype:e.fieldtype,type:e.type,config:void 0!==e.pipeline?{advancedColumns:(e.pipeline.sourceFields??[]).map(e=>({...e,config:e.config??{}})),transformers:e.pipeline.transformers}:e.config,locale:e.locale}},E=e=>{let{t:l}=(0,y.useTranslation)();return(0,u.useMemo)(()=>{let i={};e.filter(e=>e.key!==N&&e.type!==A).forEach(e=>{(Array.isArray(e.group)?e.group.some(e=>Array.isArray(e))?e.group:[e.group]:"string"==typeof e.group?[e.group]:[String(e.group)]).forEach(l=>{let n;n="string"==typeof l?l.split("."):Array.isArray(l)?l.map(e=>String(e)):[String(l)];let t=i;n.forEach((l,i)=>{(0,P.isNil)(t[l])&&(t[l]={items:[],subGroups:{}}),i===n.length-1?t[l].items.push(e):t=t[l].subGroups})})});let n=0,t=e=>Object.entries(e).reduce((e,i)=>{let[a,o]=i,r=t(o.subGroups),s=o.items.map(e=>{let i=e.key;if(!(0,P.isNil)(e.config)&&"fieldDefinition"in e.config){let l=e.config.fieldDefinition;i=(null==l?void 0:l.title)??e.key}return{key:e.key,label:l(i),meta:e}});return(s.length>0||r.length>0)&&e.push({key:`group-${n++}`,label:l(a),items:s,children:r}),e},[]);return t(i)},[e,l])},D=e=>{let{entity:l,classDefinitionId:i,columns:n,columnConfig:t,open:a,title:o,onApply:r,onCancel:s,language:d,onLanguageChange:m,exportableOnly:g=!1,renderEditor:h}=e,{t:x}=(0,y.useTranslation)(),v=(0,f.useAlertModal)(),{getByName:b}=(0,M.useClassDefinitions)(),j=p().useMemo(()=>{var e;return void 0!==i?i:(null==(e=b(l))?void 0:e.id)??l},[i,l,b]),[C,S]=(0,u.useState)([]),[T,w]=(0,u.useState)(!1),k=(0,u.useRef)(null),I=void 0!==t&&!T,{data:P}=F.api.endpoints.dataObjectGetAvailableGridColumns.useQuery({classId:j,folderId:1},{skip:!I}),z=(null==P?void 0:P.columns)??[],L=g?z.filter(e=>!0===e.exportable):z,D=(0,u.useCallback)(e=>{var l;null==(l=k.current)||l.addColumn(e)},[]),B=E(L),_=L.find(e=>e.type===A||e.key===N),G=e=>{S(e),w(!0)},H=()=>{w(!1),s()},X=(0,c.jsx)(f.ModalTitle,{iconName:"settings",children:o});return I?(0,c.jsx)($,{legacyConfig:t,onClose:H,onConfirm:()=>{var e;let l=(null==(e=k.current)?void 0:e.getColumns())??C;0===l.length?v.warn({title:x("data-hub.migration-modal.confirm-empty-columns-title"),content:x("data-hub.migration-modal.confirm-empty-columns-content"),okText:x("data-hub.migration-modal.confirm-empty-columns-ok"),cancelText:x("data-hub.migration-modal.confirm-empty-columns-cancel"),okCancel:!0,onOk:()=>{G(l)}}):G(l)},open:a,renderToolbarLeft:(0,c.jsxs)(f.Flex,{gap:"mini",children:[(0,c.jsx)(f.ColumnPickerPopover,{groups:B,onSelect:e=>{void 0!==e.meta&&D(e.meta)},placement:"leftBottom",children:(0,c.jsx)(f.IconTextButton,{icon:{value:"new"},children:x("data-hub.column-config-modal.add-column")})}),void 0!==_&&(0,c.jsx)(f.IconTextButton,{icon:{value:"new"},onClick:()=>{D(_)},children:x("data-hub.column-config-modal.add-advanced-column")})]}),title:o,children:(0,c.jsx)(f.Flex,{style:{flex:1,minWidth:0,overflow:"hidden"},vertical:!0,children:h({ref:k,classDefinitionId:j,columns:C,entity:l,hideToolbar:!0,exportableOnly:g,language:d,onLanguageChange:m,onApply:e=>{S(e)},onCancel:()=>{}})})}):(0,c.jsx)(f.Modal,{footer:null,onCancel:H,open:a,size:"XL",title:X,children:h({ref:k,classDefinitionId:j,columns:T?C:n,entity:l,hideToolbar:!1,exportableOnly:g,language:d,onLanguageChange:m,onApply:e=>{r(e),H()},onCancel:H})})},B=(0,i(3888).FB)(),_=e=>{let{value:l}=e,i=(0,y.useInjection)(y.serviceIds["DynamicTypes/AdvancedGridCellRegistry"]),n=l.map((e,l)=>{let n=i.hasDynamicType(e.type),t=`${e.type.replace(/\./g,"_")}-${l}`;return B.accessor(t,{header:e.type,meta:{editable:!1,type:n?e.type:"dataobject.adapter",config:{...n?{}:{dataObjectType:e.type,dataObjectConfig:{}}}}})}),t={};return l.forEach((e,l)=>{t[`${e.type.replace(/\./g,"_")}-${l}`]=e.value}),(0,c.jsx)(f.GridContentRenderer,{children:(0,c.jsx)(f.Grid,{autoWidth:!0,columns:n,data:[t]})})},G=e=>{var l;let{column:i,objectId:n,pipelineValue:t}=e,{t:a}=(0,y.useTranslation)(),{currentLanguage:o}=(0,M.useLanguageSelection)(),r=void 0!==t&&Object.keys(t).length>0?t:i.pipeline,s=!0===i.localizable?i.locale??o:void 0,{data:d,error:p,isFetching:m}=F.api.endpoints.dataObjectGetGridPreview.useQuery({body:{objectId:n,column:{type:i.type,key:i.key,locale:s,config:void 0!==r?{advancedColumns:r.sourceFields??[],transformers:r.transformers}:void 0}}}),g=(0,u.useRef)(d);if(void 0!==d&&(g.current=d),void 0!==p){let e="error"in p?p.error:a("data-hub.column-config-modal.preview.error");return(0,c.jsx)(f.Text,{type:"danger",children:e})}if(m&&void 0===g.current)return(0,c.jsx)(f.Text,{type:"secondary",children:a("data-hub.column-config-modal.preview.loading")});let h=null==(l=g.current)?void 0:l.value;return null!=h&&Array.isArray(h)&&0!==h.length?(0,c.jsx)(_,{value:h}):(0,c.jsx)(f.Text,{type:"secondary",children:a("data-hub.column-config-modal.preview.no-data")})},H=e=>{let{column:l,objectId:i,pipelineValue:n}=e,{t}=(0,y.useTranslation)(),[a,o]=(0,u.useState)(n);return(0,u.useEffect)(()=>{let e=setTimeout(()=>{o(n)},300);return()=>{clearTimeout(e)}},[n]),(0,c.jsx)(f.Box,{padding:{top:"small",bottom:"none",x:"small"},children:(0,c.jsxs)(f.Flex,{align:"center",gap:"small",children:[(0,c.jsxs)(f.Text,{style:{wordBreak:"keep-all"},children:[t("grid.advanced-column.preview"),":"]}),null===i?(0,c.jsx)(f.Text,{type:"secondary",children:t("data-hub.column-config-modal.preview.placeholder")}):(0,c.jsx)(G,{column:l,objectId:i,pipelineValue:a})]})})},X=e=>{let{column:l,entity:i,config:n,objectId:t,value:a,onChange:o,sourceFieldsRegistryId:r,transformersRegistryId:s}=e,{t:d}=(0,y.useTranslation)(),[p]=f.Form.useForm(),{compact:m}=I(),[g,h]=(0,u.useState)(a??{}),x=(0,c.jsx)(f.Pipeline.DynamicGroupItem,{dynamicTypeRegistryId:r,id:"sourceFields",showTitle:!m,translationKeyPrefix:"data-hub.column-config-modal.pipeline"}),v=(0,c.jsx)(f.Pipeline.DynamicGroupItem,{dynamicTypeRegistryId:s,id:"transformers",showTitle:!m,translationKeyPrefix:"data-hub.column-config-modal.pipeline"}),b=m?(0,c.jsx)(f.Tabs,{items:[{key:"sourceFields",label:d("data-hub.column-config-modal.pipeline.sourceFields"),forceRender:!0,children:x},{key:"transformers",label:d("data-hub.column-config-modal.pipeline.transformers"),forceRender:!0,children:v}]}):(0,c.jsx)(f.SplitLayout,{leftItem:{children:x,size:50},rightItem:{children:v,size:50},withDivider:!0});return(0,u.useEffect)(()=>{p.setFieldValue("value",a??{})},[a]),(0,c.jsx)(f.Form,{form:p,initialValues:{value:a??{}},layout:"vertical",onValuesChange:e=>{let l=p.getFieldValue("value");void 0===l||(0,P.isEqual)(g,l)||(h(l),null==o||o(l))},children:(0,c.jsx)(f.PipelineConfigProvider,{initialConfig:n??{},children:(0,c.jsx)(f.Form.Item,{name:"value",children:(0,c.jsx)(f.Pipeline,{items:[{id:"title",component:(0,c.jsx)(f.Pipeline.CustomItem,{children:(0,c.jsx)(f.Box,{padding:{top:"mini",bottom:"mini",x:"none"},children:(0,c.jsx)(f.Form.Item,{name:"title",children:(0,c.jsx)(f.Input,{placeholder:d("data-hub.column-config-modal.pipeline.title"),style:{maxWidth:"100%"}})})})})},{id:"fields",component:(0,c.jsx)(f.Pipeline.CustomItem,{children:b})},{id:"preview",component:(0,c.jsx)(f.Pipeline.CustomItem,{children:void 0!==l&&(0,c.jsx)(H,{column:l,objectId:t??null,pipelineValue:g})})}],value:a??{}})})})})},R=e=>{let{column:l,entity:i,objectId:n,onPipelineChange:t,sourceFieldsRegistryId:a,transformersRegistryId:o}=e;return(0,c.jsx)(X,{column:l,config:l.pipelineConfig,entity:i,objectId:n,onChange:e=>{t(l._id,e)},sourceFieldsRegistryId:a,transformersRegistryId:o,value:l.pipeline})};var O=i(8972);let W=e=>{let{value:l,onChange:i}=e,n=(0,O.useUser)(),t=["-",...Array.isArray(n.contentLanguages)?n.contentLanguages:[]];return(0,c.jsx)(f.LanguageSelection,{languages:t,onSelectLanguage:e=>{i("-"===e?null:e)},selectedLanguage:l??"-"})};var V=i(7925),U=i(5303);let Y=(0,m.rU)(e=>{let{token:l,css:i}=e;return{body:i`
    display: flex;
    gap: ${l.marginSM}px;
    height: 100%;
  `,fieldsPanel:i`
    display: flex;
    flex-direction: column;
    gap: ${l.marginXS}px;
    width: 280px;
    height: 100%;
    padding-right: ${l.paddingSM}px;
    border-right: 1px solid ${l.colorBorderSecondary};
  `,list:i`
    flex: 1;
    min-width: 0;
    height: 100%;
    overflow-y: auto;
  `}}),Q=e=>{let{groups:l,onColumnSelect:i,onClose:n}=e,{t}=(0,y.useTranslation)(),{styles:a}=Y();return(0,c.jsxs)("div",{className:a.fieldsPanel,children:[(0,c.jsx)(f.Header,{fullWidth:!0,title:t("data-hub.column-config-modal.fields-to-add"),children:(0,c.jsx)(f.Flex,{className:"w-full",justify:"flex-end",children:(0,c.jsx)(f.IconButton,{icon:{value:"collapse-sidebar",colorToken:"colorPrimary"},onClick:n})})}),(0,c.jsx)(f.ColumnPicker,{fillHeight:!0,groups:l,onSelect:e=>{void 0!==e.meta&&i(e.meta)}})]})};var K=i(3842);let q=[{key:"id",type:"system.id",group:["system"],config:[]},{key:"fullpath",type:"system.string",group:["system"],config:[]}],J=(0,u.forwardRef)(function(e,l){var i;let{entity:n,classDefinitionId:t,columns:a,onApply:o,onCancel:r,hideToolbar:s=!1,sourceFieldsRegistryId:d,transformersRegistryId:p,language:m,onLanguageChange:g,exportableOnly:h=!1}=e,{t:x}=(0,y.useTranslation)(),{styles:v}=Y(),b=(0,O.useUser)(),[j,C]=(0,u.useState)(!0),S=m??(null==(i=b.contentLanguages)?void 0:i[0])??"en",[T,w]=(0,u.useState)(S),[k,I]=(0,u.useState)(!1);(0,u.useEffect)(()=>{null==g||g(T)},[]);let $=(0,u.useCallback)(e=>{w(e),null==g||g(e)},[g]),D=(0,u.useMemo)(()=>({currentLanguage:T,setCurrentLanguage:$,hasLocalizedFields:k,setHasLocalizedFields:I}),[T,$,k]),{draft:B,isLoading:_,objectId:G,columnGroups:H,onAddAdvancedColumn:X,openElementSelector:J,handleAddColumnOfType:Z,handlePipelineChange:ee,handleRemove:el,handleApply:ei,handleDiscard:en,handleLocaleChange:et,handleReorder:ea,getColumns:eo}=(e=>{let{entity:l,classDefinitionId:i,columns:n,onApply:t,onCancel:a,exportableOnly:o=!1}=e,{getByName:r}=(0,M.useClassDefinitions)(),s=(0,u.useMemo)(()=>{var e;return(0,P.isNil)(i)?(null==(e=r(l))?void 0:e.id)??l:i},[i,l,r]),{data:d,isLoading:c}=F.api.endpoints.dataObjectGetAvailableGridColumns.useQuery({classId:s,folderId:1}),[p,m]=(0,u.useState)(()=>n.map(z));(0,u.useEffect)(()=>{m(n.map(z))},[n]);let[g,h]=(0,u.useState)(null),x=(0,u.useRef)(!1),{data:f}=F.api.endpoints.dataObjectGetGrid.useQuery({classId:s,body:{folderId:1,columns:q,filters:{includeDescendants:!0,page:1,pageSize:1}}},{skip:void 0===s});(0,u.useEffect)(()=>{var e;if(x.current)return;let l=null==f||null==(e=f.items)?void 0:e[0];(null==l?void 0:l.id)!==void 0&&h(l.id)},[null==f?void 0:f.items]);let{open:y}=(0,K.useElementSelector)({selectionType:K.SelectionType.Single,areas:{object:!0,asset:!1,document:!1},config:{objects:{allowedTypes:["object"],...void 0!==l?{allowedClasses:[l]}:{}}},onFinish:e=>{var l;let i=null==e||null==(l=e.items)?void 0:l[0];void 0!==i&&(x.current=!0,h(i.data.id))}}),v=(null==d?void 0:d.columns)??[];(0,u.useEffect)(()=>{0!==v.length&&m(e=>e.map(e=>{let l=v.find(l=>l.key===e.key||e.type===A&&l.type===e.type);return void 0===l?e:{...e,localizable:e.localizable??l.localizable,pipelineConfig:e.pipelineConfig??l.config}}))},[v]);let b=(0,u.useCallback)(e=>{m(l=>[...l,{_id:crypto.randomUUID(),key:e.key,fieldtype:e.key,type:e.type,pipelineConfig:e.config,localizable:e.localizable,isNew:!0}])},[]),j=(0,u.useMemo)(()=>o?v.filter(e=>!0===e.exportable):v,[v,o]),C=E(j),S=(0,u.useMemo)(()=>j.find(e=>e.type===A||e.key===N),[j]);return{draft:p,isLoading:c,objectId:g,availableFields:v,columnGroups:C,onAddAdvancedColumn:void 0!==S?()=>{b(S)}:void 0,openElementSelector:y,handleAddColumnOfType:b,handlePipelineChange:(e,l)=>{m(i=>i.map(i=>i._id===e?{...i,pipeline:l}:i))},handleRemove:e=>{m(l=>l.filter(l=>l._id!==e))},handleApply:()=>{t(p.filter(e=>""!==e.key).map(L))},handleDiscard:()=>{m(n.map(z)),a()},handleLocaleChange:(e,l)=>{m(i=>i.map(i=>i._id===e?{...i,locale:l}:i))},handleReorder:e=>{m(l=>e.map(e=>l.find(l=>l._id===e)).filter(e=>void 0!==e))},getColumns:()=>p.filter(e=>""!==e.key).map(L)}})({entity:n,classDefinitionId:t,columns:a,onApply:o,onCancel:r,exportableOnly:h});(0,u.useImperativeHandle)(l,()=>({getColumns:eo,addColumn:Z}),[eo,Z]);let er=B.map(e=>{var l,i;let t=e.type===A,a=""===e.key?"":e.key;return{id:e._id,sortable:!0,type:t?"collapse":"default",defaultActive:t&&!0===e.isNew,children:t?(0,c.jsx)(V.A,{color:"purple",children:(0,P.isNil)(null==(l=e.pipeline)?void 0:l.title)?a:String(null==(i=e.pipeline)?void 0:i.title)}):(0,c.jsx)(V.A,{children:a}),...t?{body:void 0!==e.pipelineConfig?(0,c.jsx)(R,{column:e,entity:n,objectId:G,onPipelineChange:ee,sourceFieldsRegistryId:d,transformersRegistryId:p}):(0,c.jsx)(f.Spin,{})}:{},renderRightToolbar:(0,c.jsxs)(f.Space,{size:"mini",children:[!0===e.localizable&&t&&(0,c.jsx)(W,{onChange:l=>{et(e._id,l)},value:e.locale}),(0,c.jsx)(f.IconButton,{icon:{value:"trash"},onClick:()=>{el(e._id)},theme:"secondary"})]})}});return _?(0,c.jsx)(f.Flex,{align:"center",justify:"center",style:{minHeight:200},children:(0,c.jsx)(f.Spin,{})}):(0,c.jsx)(M.LanguageSelectionContext.Provider,{value:D,children:(0,c.jsx)(f.ContentLayout,{renderToolbar:s?void 0:(0,c.jsxs)(f.Toolbar,{padding:{x:"none",y:"small"},theme:"secondary",children:[(0,c.jsxs)(f.Flex,{gap:"mini",children:[(0,c.jsx)(f.IconTextButton,{icon:{value:"new"},onClick:()=>{C(e=>!e)},type:"default",children:x("data-hub.column-config-modal.add-column")}),void 0!==X&&(0,c.jsx)(f.IconTextButton,{icon:{value:"new"},onClick:X,type:"default",children:x("data-hub.column-config-modal.add-advanced-column")})]}),(0,c.jsxs)(f.Space,{size:"extra-small",children:[(0,c.jsx)(f.Button,{onClick:en,type:"default",children:x("data-hub.column-config-modal.discard")}),(0,c.jsx)(f.Button,{onClick:ei,type:"primary",children:x("data-hub.column-config-modal.apply")})]})]}),renderTopBar:(0,c.jsxs)(f.Toolbar,{align:"center",position:"content",theme:"secondary",children:[(0,c.jsx)(f.Button,{onClick:J,children:x("data-hub.column-config-modal.preview.select-object")}),(0,c.jsx)(M.LanguageSelectionWithProvider,{})]}),children:(0,c.jsx)(f.Content,{padded:!0,padding:{x:"none",y:"small"},style:{height:"calc(80vh - 200px)"},children:(0,c.jsxs)("div",{className:v.body,children:[!s&&j&&(0,c.jsx)(Q,{groups:H,onClose:()=>{C(!1)},onColumnSelect:Z}),(0,c.jsx)("div",{className:v.list,children:(0,c.jsxs)(f.Space,{direction:"vertical",style:{width:"100%"},children:[0===B.length&&(0,c.jsx)(U.A,{image:U.A.PRESENTED_IMAGE_SIMPLE}),B.length>0&&(0,c.jsx)(f.StackList,{items:er,onItemsChange:e=>{ea(e.map(e=>String(e.id)))},sortable:!0})]})})]})})})})});var Z=i(1436),ee=i(969);let el=(0,m.rU)(e=>{let{css:l}=e;return{fullWidth:l`
      width: 100%;
    `}}),ei="YYYY-MM-DD HH:mm",en=()=>{let{t:e}=(0,y.useTranslation)(),{styles:l}=el(),[i]=f.Form.useForm(),{dateFrom:n,setDateFrom:t,dateTo:a,setDateTo:o,relatedObjectId:r,setRelatedObjectId:s,message:d,setMessage:u,pid:p,setPid:m,resetFilters:g,updateFilters:h,isLoading:x}=(0,ee.useFilter)();return(0,c.jsx)(f.ContentLayout,{renderToolbar:(0,c.jsxs)(f.Toolbar,{theme:"secondary",children:[(0,c.jsx)(f.IconTextButton,{disabled:x,icon:{value:"close"},onClick:()=>{g(),i.resetFields()},type:"link",children:e("sidebar.clear-all-filters")}),(0,c.jsx)(f.Button,{disabled:x,loading:x,onClick:h,type:"primary",children:e("button.apply")})]}),children:(0,c.jsx)(f.Content,{padded:!0,children:(0,c.jsx)(f.Form,{form:i,layout:"vertical",children:(0,c.jsxs)(f.Space,{className:l.fullWidth,direction:"vertical",size:"none",children:[(0,c.jsx)(f.Title,{children:e("application-logger.sidebar.search-parameter")}),(0,c.jsx)(f.Form.Item,{label:e("application-logger.filter.date-from"),name:"dateFrom",children:(0,c.jsx)(f.DatePicker,{className:"w-full",format:ei,onChange:e=>{t(e)},outputType:"dateString",showTime:{format:"HH:mm"},value:n})}),(0,c.jsx)(f.Form.Item,{label:e("application-logger.filter.date-to"),name:"dateTo",children:(0,c.jsx)(f.DatePicker,{className:"w-full",format:ei,onChange:e=>{o(e)},outputType:"dateString",showTime:{format:"HH:mm"},value:a})}),(0,c.jsx)(f.Form.Item,{label:e("application-logger.filter.priority"),name:"priority",children:(0,c.jsx)(ee.PrioritySelect,{})}),(0,c.jsx)(f.Form.Item,{label:e("application-logger.filter.message"),name:"message",children:(0,c.jsx)(f.Input,{onChange:e=>{let l=e.target.value;u(""===l?null:l)},value:d??void 0})}),(0,c.jsx)(f.Form.Item,{label:e("application-logger.filter.related-object-id"),name:"relatedObjectId",children:(0,c.jsx)(f.Input,{min:"0",onChange:e=>{let l=e.target.value;s(""===l?null:Number.parseInt(l))},step:"1",type:"number",value:r??void 0})}),(0,c.jsx)(f.Form.Item,{label:e("application-logger.filter.pid"),name:"pid",children:(0,c.jsx)(f.Input,{min:"0",onChange:e=>{let l=e.target.value;m(""===l?null:Number.parseInt(l))},step:"1",type:"number",value:p??void 0})})]})})})})},et=e=>{let{children:l}=e,[i,n]=(0,u.useState)("filter"),t=(0,u.useMemo)(()=>({entries:[],buttons:[],sizing:"default",highlights:[],activeTab:i,setEntries:()=>{},setButtons:()=>{},setSizing:()=>{},setHighlights:()=>{},setActiveTab:n,addEntry:()=>{},removeEntry:()=>{},addButton:()=>{},removeButton:()=>{},toggleHighlight:()=>{},openTab:e=>{n(e)},closeTab:()=>{n("")},toggleTab:e=>{n(l=>l===e?"":e)}}),[i]);return(0,c.jsx)(f.SidebarContext.Provider,{value:t,children:l})},ea=e=>(0,c.jsx)(ee.FilterProvider,{children:(0,c.jsx)(eo,{...e})}),eo=e=>{let{componentPrefix:l,configName:i}=e,{t:n}=(0,y.useTranslation)(),t=[{key:"filter",icon:(0,c.jsx)(f.Icon,{options:{width:"16px",height:"16px"},value:"filter"}),component:(0,c.jsx)(en,{})}],a=(0,y.useAppDispatch)(),[o,r]=(0,u.useState)(1),[s,d]=(0,u.useState)(20),[p,m]=(0,u.useState)([]),{columnFilters:g,setIsLoading:h}=(0,ee.useFilter)(),x=[...g,{key:"component",type:"equals",filterValue:l+i}],{data:v,isFetching:b}=(0,ee.useBundleApplicationLoggerGetCollectionQuery)({body:{filters:{page:o,pageSize:s,columnFilters:x,sortFilter:(0,ee.mapSortingToSortFilter)(p)}}}),j=(null==v?void 0:v.totalItems)??0,C=(0,u.useCallback)(()=>{a(ee.api.util.invalidateTags(Z.invalidatingTags.APPLICATION_LOGGER()))},[a]),{refreshInterval:S,setRefreshInterval:T}=(e=>{let[l,i]=(0,u.useState)(void 0),n=(0,u.useCallback)(e,[e]);return(0,u.useEffect)(()=>{if((0,P.isNil)(l))return;let e=setInterval(()=>{n()},1e3*Number.parseInt(l));return()=>{clearInterval(e)}},[l,n]),{refreshInterval:l,setRefreshInterval:i}})(C);return(0,u.useEffect)(()=>{h(b)},[b]),(0,c.jsx)(et,{children:(0,c.jsx)(f.ContentLayout,{className:"h-full",renderSidebar:(0,c.jsx)(f.Sidebar,{entries:t}),renderToolbar:(0,c.jsxs)(f.Toolbar,{justify:"space-between",theme:"secondary",children:[(0,c.jsxs)(f.Flex,{align:"center",gap:8,children:[!(0,P.isNil)(S)&&(0,c.jsx)("span",{children:n("application-logger.refresh-interval")}),(0,c.jsx)(f.CreatableSelect,{allowClear:!0,inputType:"number",minWidth:200,numberInputProps:{min:1},onChange:T,onCreateOption:e=>({value:e,label:n("application-logger.refresh-interval.seconds",{seconds:e})}),options:[{value:"3",label:n("application-logger.refresh-interval.seconds",{seconds:3})},{value:"5",label:n("application-logger.refresh-interval.seconds",{seconds:5})},{value:"10",label:n("application-logger.refresh-interval.seconds",{seconds:10})},{value:"30",label:n("application-logger.refresh-interval.seconds",{seconds:30})},{value:"60",label:n("application-logger.refresh-interval.seconds",{seconds:60})}],placeholder:n("application-logger.refresh-interval.select"),validate:e=>!Number.isNaN(Number.parseInt(e))&&Number.parseInt(e)>0,value:S})]}),(0,c.jsxs)(f.Flex,{children:[(0,c.jsx)(f.IconButton,{disabled:b,icon:{value:"refresh"},onClick:C}),j>0&&(0,c.jsxs)(c.Fragment,{children:[(0,c.jsx)(f.Divider,{size:"small",type:"vertical"}),(0,c.jsx)(f.Pagination,{current:o,defaultPageSize:s,onChange:(e,l)=>{r(e),d(l)},showSizeChanger:!0,showTotal:e=>n("pagination.show-total",{total:e}),total:j})]})]})]}),children:(0,c.jsx)(f.Content,{padded:!0,children:(0,c.jsx)(ee.ApplicationLoggerTable,{isLoading:b,items:(null==v?void 0:v.items)??[],onSortingChange:e=>{m(e),r(1)},sorting:p})})})})}}}]);
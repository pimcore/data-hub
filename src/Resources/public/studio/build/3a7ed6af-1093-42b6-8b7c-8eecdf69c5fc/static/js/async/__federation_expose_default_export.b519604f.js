/*! For license information please see __federation_expose_default_export.b519604f.js.LICENSE.txt */
"use strict";(self["chunk_pimcore_datahub_bundle "]=self["chunk_pimcore_datahub_bundle "]||[]).push([["525"],{7673(e,i,l){l.r(i),l.d(i,{BaseDetailView:()=>d.Wc,ColumnLocaleControl:()=>W,stringifyYaml:()=>C,ADVANCED_COLUMN_KEY:()=>P,useCompactLayout:()=>$,advancedToSchemaColumn:()=>L,ADVANCED_COLUMN_TYPE:()=>z,BaseColumnEditor:()=>Z,CompactLayoutProvider:()=>k,ColumnEditorItemBody:()=>X,DynamicTypeDataHubAdapterAbstract:()=>r.E,DynamicTypeDataHubAdapterRegistry:()=>s.f,DataHubLogTab:()=>eo,PermissionsTab:()=>a.K,ConfigToolbar:()=>d.MM,advancedFromSchemaColumn:()=>A,GeneralTab:()=>t.a,ColumnConfigModal:()=>D,ConfigSummary:()=>b,parseYaml:()=>S,trackConfigError:()=>d.Mv,useAddColumnGroups:()=>E,StatusTag:()=>x,ColumnPreview:()=>_,ConfigContainer:()=>n.y,isValidYaml:()=>w,bundleServiceIds:()=>o.s,useDetailView:()=>d.iO,MigrationModal:()=>I,ColumnPipelineForm:()=>R});var n=l(6100),t=l(970),a=l(6427),o=l(17),r=l(5667),s=l(8576),d=l(2379),c=l(4848),u=l(6247),p=l.n(u),m=l(4781),g=l(2696);let h={added:"green",changed:"gold",removed:"red",moved:"geekblue"},x=e=>{let{status:i}=e,{t:l}=(0,m.useTranslation)();return(0,c.jsx)(g.Tag,{color:h[i],"data-review-mark":"",style:{marginInlineEnd:0},children:l(`data-hub.review.status.${i}`)})},f=(0,m.createStyles)(e=>{let{token:i,css:l}=e;return{summary:l`
    min-width: 0;
  `,head:l`
    display: flex;
    flex-direction: column;
    gap: ${i.marginXXS}px;
    padding: ${i.paddingXS}px 0 ${i.paddingSM}px;
    border-bottom: 1px solid ${i.colorSplit};
  `,identity:l`
    display: flex;
    align-items: center;
    gap: ${i.marginXS}px;
  `,name:l`
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: ${i.fontSizeLG}px;
    font-weight: ${i.fontWeightStrong};
    color: ${i.colorText};
  `,pill:l`
    flex: none;
    display: inline-flex;
    align-items: center;
    gap: ${i.marginXXS}px;
    height: ${i.controlHeightSM-2}px;
    padding: 0 ${i.paddingXS}px;
    border-radius: ${i.borderRadiusSM}px;
    background: ${i.colorFillTertiary};
    color: ${i.colorTextSecondary};
    font-size: ${i.fontSize}px;
    line-height: 1;
  `,dot:l`
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: ${i.colorTextQuaternary};
  `,dotOn:l`
    background: ${i.colorSuccess};
  `,description:l`
    font-size: ${i.fontSize}px;
    line-height: ${i.lineHeightSM};
    color: ${i.colorTextSecondary};
    overflow-wrap: anywhere;
  `,spine:l`
    display: grid;
    grid-template-columns: ${i.marginXS}px minmax(0, 1fr);
    column-gap: ${i.marginSM}px;
    padding: ${i.padding}px 0;
  `,mark:l`
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: ${(i.fontSizeSM*i.lineHeightSM-7)/2}px;
  `,node:l`
    flex: none;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: ${i.colorPrimary};
  `,nodeMuted:l`
    background: ${i.colorBgContainer};
    box-shadow: inset 0 0 0 1px ${i.colorPrimaryBorder};
  `,line:l`
    flex: 1;
    width: 1px;
    margin: ${i.marginXXS}px 0;
    background: ${i.colorPrimaryBorder};
  `,entry:l`
    min-width: 0;
    padding-bottom: ${i.padding}px;
  `,entryLast:l`
    min-width: 0;
  `,role:l`
    font-size: ${i.fontSizeSM}px;
    font-weight: ${i.fontWeightStrong};
    letter-spacing: .07em;
    text-transform: uppercase;
    color: ${i.colorTextTertiary};
    line-height: ${i.lineHeightSM};
  `,section:l`
    /* block-level, not inline: an inline button rides the entry's baseline and lands a few
       pixels below the row it is meant to head */
    display: flex;
    width: fit-content;
    align-items: center;
    gap: ${i.marginXXS}px;
    max-width: 100%;
    margin: 0 0 ${i.marginXXS}px;
    padding: 0;
    border: none;
    background: none;
    font: inherit;
    font-size: ${i.fontSizeSM}px;
    font-weight: ${i.fontWeightStrong};
    letter-spacing: .07em;
    text-transform: uppercase;
    line-height: ${i.lineHeightSM};
    color: ${i.colorPrimary};
    cursor: pointer;

    &:hover {
      color: ${i.colorPrimaryHover};
    }
  `,value:l`
    font-weight: ${i.fontWeightStrong};
    color: ${i.colorText};
    font-variant-numeric: tabular-nums;
    overflow-wrap: anywhere;
  `,note:l`
    font-size: ${i.fontSizeSM}px;
    line-height: ${i.lineHeightSM};
    color: ${i.colorTextTertiary};
    overflow-wrap: anywhere;
  `,field:l`
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: ${i.marginXS}px;
    min-height: ${i.controlHeightSM}px;

    /* the marks are tall for their row; without this they read as one block */
    & + & {
      margin-top: ${i.marginXXS}px;
    }
  `,fieldLabel:l`
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-weight: ${i.fontWeightStrong};
    color: ${i.colorText};
  `,foot:l`
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding: ${i.paddingSM}px 0;
    border-top: 1px solid ${i.colorSplit};
    font-variant-numeric: tabular-nums;
  `,footLead:l`
    font-size: ${i.fontSize}px;
    line-height: ${i.lineHeightSM};
    color: ${i.colorTextSecondary};

    b {
      color: ${i.colorText};
    }
  `,footGroups:l`
    font-size: ${i.fontSizeSM}px;
    line-height: ${i.lineHeightSM};
    color: ${i.colorTextTertiary};
  `}}),y={width:12,height:12},v="data-hub.review",b=e=>{let{name:i,description:l,active:n,sections:t,variant:a="changes",foot:o,activeKey:r,onOpenSection:s}=e,{t:d}=(0,m.useTranslation)(),{styles:u,cx:h}=f(),b=t.reduce((e,i)=>e+i.rows.length,0),j=e=>"description"===a?(0,c.jsxs)(p().Fragment,{children:[(0,c.jsx)("div",{className:u.value,title:e.hint,children:e.label}),void 0!==e.note&&""!==e.note&&(0,c.jsx)("div",{className:u.note,children:e.note})]},e.key):(0,c.jsxs)("div",{className:u.field,title:e.hint,children:[(0,c.jsx)("span",{className:u.fieldLabel,children:e.label}),void 0!==e.status&&(0,c.jsx)(x,{status:e.status})]},e.key),S=void 0!==n&&(0,c.jsxs)("span",{className:u.pill,children:[(0,c.jsx)("span",{className:h(u.dot,n&&u.dotOn)}),d(n?`${v}.active`:`${v}.inactive`)]});return(0,c.jsxs)("div",{className:u.summary,children:[(0,c.jsxs)("div",{className:u.head,children:[(0,c.jsxs)("div",{className:u.identity,children:[(0,c.jsx)("span",{className:u.name,title:i,children:i}),"description"===a?(0,c.jsxs)(c.Fragment,{children:[(0,c.jsx)(g.Tag,{color:"green",style:{marginInlineEnd:0},children:d(`${v}.new`)}),S]}):(0,c.jsxs)(c.Fragment,{children:[S,(0,c.jsx)(g.Tag,{color:"gold",style:{marginInlineEnd:0},children:d(1===b?`${v}.changed-field`:`${v}.changed-fields`).replace("%s",String(b))})]})]}),void 0!==l&&""!==l&&(0,c.jsx)("div",{className:u.description,children:l})]}),(0,c.jsx)("div",{className:u.spine,children:t.map((e,i)=>(0,c.jsxs)(p().Fragment,{children:[(0,c.jsxs)("div",{className:u.mark,children:[(0,c.jsx)("span",{className:h(u.node,e.key!==r&&u.nodeMuted)}),i<t.length-1&&(0,c.jsx)("span",{className:u.line})]}),(0,c.jsxs)("div",{className:i===t.length-1?u.entryLast:u.entry,children:[void 0===s?(0,c.jsx)("div",{className:u.role,children:e.label}):(0,c.jsxs)("button",{className:u.section,onClick:()=>{s(e.key)},title:d(`${v}.open-section`),type:"button",children:[(0,c.jsx)("span",{children:e.label}),(0,c.jsx)(g.Icon,{options:y,value:"arrow-narrow-right"})]}),e.rows.map(j)]})]},e.key))}),void 0!==o&&(0,c.jsxs)("div",{className:u.foot,children:[(0,c.jsx)("div",{className:u.footLead,children:o.lead.split("**").map((e,i)=>i%2==1?(0,c.jsx)("b",{children:e},`b${i}`):(0,c.jsx)(p().Fragment,{children:e},`t${i}`))}),void 0!==o.detail&&""!==o.detail&&(0,c.jsx)("div",{className:u.footGroups,children:o.detail})]})]})};var j=l(1505);let S=e=>(0,j.parse)(e),C=(e,i)=>(0,j.stringify)(e,{indent:2,...i}),w=e=>{try{return(0,j.parse)(e),!0}catch{return!1}},T=(0,u.createContext)({compact:!1}),k=e=>{let{children:i,compact:l=!1}=e;return(0,u.useMemo)(()=>(0,c.jsx)(T.Provider,{value:{compact:l},children:i}),[i,l])},$=()=>(0,u.useContext)(T),I=e=>{let{open:i,title:l,iconName:n="settings",legacyConfig:t,renderToolbarLeft:a,onConfirm:o,onClose:r,children:s}=e,{t:d}=(0,m.useTranslation)(),[p,h]=(0,u.useState)(!1),x=C(t),f=(0,c.jsx)(g.ModalTitle,{iconName:n,children:l});return p?(0,c.jsxs)(g.Modal,{footer:null,onCancel:()=>{h(!1)},open:i,size:"XL",title:f,children:[(0,c.jsxs)(g.Flex,{gap:"small",style:{height:"calc(80vh - 120px)",overflow:"hidden"},children:[(0,c.jsx)(g.Flex,{style:{flex:1,minWidth:0,overflow:"hidden"},vertical:!0,children:(0,c.jsx)(k,{compact:!0,children:s})}),(0,c.jsx)("div",{style:{width:1,background:"var(--ant-color-split, rgba(0,0,0,.06))",flexShrink:0}}),(0,c.jsxs)(g.Flex,{gap:"small",style:{flex:1,minWidth:0,overflow:"auto"},vertical:!0,children:[(0,c.jsx)(g.Alert,{description:d("data-hub.migration-modal.legacy-notice"),showIcon:!0,type:"warning"}),(0,c.jsx)(g.CodeEditor,{height:"100%",preset:"yaml",readOnly:!0,value:x})]})]}),(0,c.jsxs)(g.Toolbar,{padding:{x:"none",y:"small"},theme:"secondary",children:[a,(0,c.jsxs)(g.Space,{size:"extra-small",children:[(0,c.jsx)(g.Button,{onClick:()=>{h(!1)},type:"default",children:d("data-hub.migration-modal.cancel")}),(0,c.jsx)(g.Button,{onClick:o,type:"primary",children:d("data-hub.migration-modal.confirm-migration")})]})]})]}):(0,c.jsxs)(g.Modal,{footer:null,onCancel:r,open:i,size:"XL",title:f,children:[(0,c.jsxs)(g.Flex,{gap:"small",vertical:!0,children:[(0,c.jsx)(g.Alert,{description:d("data-hub.migration-modal.legacy-notice"),showIcon:!0,type:"warning"}),(0,c.jsx)(g.CodeEditor,{height:"400px",preset:"yaml",readOnly:!0,value:x})]}),(0,c.jsx)(g.Toolbar,{padding:{x:"none",y:"small"},theme:"secondary",children:(0,c.jsx)(g.Space,{size:"extra-small",children:(0,c.jsx)(g.Button,{onClick:()=>{h(!0)},type:"primary",children:d("data-hub.migration-modal.start-migration")})})})]})};var F=l(1161),N=l(3090),M=l(2543);let P="advanced",z="dataobject.advanced",A=e=>{var i,l;return{_id:crypto.randomUUID(),key:e.key,fieldtype:e.fieldtype,type:e.type,config:e.config,pipeline:e.type===z?{title:e.key!==P?e.key:e.title,sourceFields:((null==(i=e.config)?void 0:i.advancedColumns)??[]).map(e=>({...e,config:e.config??{}})),transformers:null==(l=e.config)?void 0:l.transformers}:void 0,locale:e.locale}},L=e=>{var i;return{key:e.type===z&&(null==(i=e.pipeline)?void 0:i.title)!==void 0&&""!==e.pipeline.title?e.pipeline.title:e.key,fieldtype:e.fieldtype,type:e.type,config:void 0!==e.pipeline?{advancedColumns:(e.pipeline.sourceFields??[]).map(e=>({...e,config:e.config??{}})),transformers:e.pipeline.transformers}:e.config,locale:e.locale}},E=e=>{let{t:i}=(0,m.useTranslation)();return(0,u.useMemo)(()=>{let l={};e.filter(e=>e.key!==P&&e.type!==z).forEach(e=>{(Array.isArray(e.group)?e.group.some(e=>Array.isArray(e))?e.group:[e.group]:"string"==typeof e.group?[e.group]:[String(e.group)]).forEach(i=>{let n;n="string"==typeof i?i.split("."):Array.isArray(i)?i.map(e=>String(e)):[String(i)];let t=l;n.forEach((i,l)=>{(0,M.isNil)(t[i])&&(t[i]={items:[],subGroups:{}}),l===n.length-1?t[i].items.push(e):t=t[i].subGroups})})});let n=0,t=e=>Object.entries(e).reduce((e,l)=>{let[a,o]=l,r=t(o.subGroups),s=o.items.map(e=>{let l=e.key;if(!(0,M.isNil)(e.config)&&"fieldDefinition"in e.config){let i=e.config.fieldDefinition;l=(null==i?void 0:i.title)??e.key}return{key:e.key,label:i(l),meta:e}});return(s.length>0||r.length>0)&&e.push({key:`group-${n++}`,label:i(a),items:s,children:r}),e},[]);return t(l)},[e,i])},D=e=>{let{entity:i,classDefinitionId:l,columns:n,columnConfig:t,open:a,title:o,onApply:r,onCancel:s,language:d,onLanguageChange:h,exportableOnly:x=!1,renderEditor:f}=e,{t:y}=(0,m.useTranslation)(),v=(0,g.useAlertModal)(),{getByName:b}=(0,N.useClassDefinitions)(),j=p().useMemo(()=>{var e;return void 0!==l?l:(null==(e=b(i))?void 0:e.id)??i},[l,i,b]),[S,C]=(0,u.useState)([]),[w,T]=(0,u.useState)(!1),k=(0,u.useRef)(null),$=void 0!==t&&!w,{data:M}=F.api.endpoints.dataObjectGetAvailableGridColumns.useQuery({classId:j,folderId:1},{skip:!$}),A=(null==M?void 0:M.columns)??[],L=x?A.filter(e=>!0===e.exportable):A,D=(0,u.useCallback)(e=>{var i;null==(i=k.current)||i.addColumn(e)},[]),B=E(L),G=L.find(e=>e.type===z||e.key===P),H=e=>{C(e),T(!0)},_=()=>{T(!1),s()},R=(0,c.jsx)(g.ModalTitle,{iconName:"settings",children:o});return $?(0,c.jsx)(I,{legacyConfig:t,onClose:_,onConfirm:()=>{var e;let i=(null==(e=k.current)?void 0:e.getColumns())??S;0===i.length?v.warn({title:y("data-hub.migration-modal.confirm-empty-columns-title"),content:y("data-hub.migration-modal.confirm-empty-columns-content"),okText:y("data-hub.migration-modal.confirm-empty-columns-ok"),cancelText:y("data-hub.migration-modal.confirm-empty-columns-cancel"),okCancel:!0,onOk:()=>{H(i)}}):H(i)},open:a,renderToolbarLeft:(0,c.jsxs)(g.Flex,{gap:"mini",children:[(0,c.jsx)(g.ColumnPickerPopover,{groups:B,onSelect:e=>{void 0!==e.meta&&D(e.meta)},placement:"leftBottom",children:(0,c.jsx)(g.IconTextButton,{icon:{value:"new"},children:y("data-hub.column-config-modal.add-column")})}),void 0!==G&&(0,c.jsx)(g.IconTextButton,{icon:{value:"new"},onClick:()=>{D(G)},children:y("data-hub.column-config-modal.add-advanced-column")})]}),title:o,children:(0,c.jsx)(g.Flex,{style:{flex:1,minWidth:0,overflow:"hidden"},vertical:!0,children:f({ref:k,classDefinitionId:j,columns:S,entity:i,hideToolbar:!0,exportableOnly:x,language:d,onLanguageChange:h,onApply:e=>{C(e)},onCancel:()=>{}})})}):(0,c.jsx)(g.Modal,{footer:null,onCancel:_,open:a,size:"XL",title:R,children:f({ref:k,classDefinitionId:j,columns:w?S:n,entity:i,hideToolbar:!1,exportableOnly:x,language:d,onLanguageChange:h,onApply:e=>{r(e),_()},onCancel:_})})},B=(0,l(3888).FB)(),G=e=>{let{value:i}=e,l=(0,m.useInjection)(m.serviceIds["DynamicTypes/AdvancedGridCellRegistry"]),n=i.map((e,i)=>{let n=l.hasDynamicType(e.type),t=`${e.type.replace(/\./g,"_")}-${i}`;return B.accessor(t,{header:e.type,meta:{editable:!1,type:n?e.type:"dataobject.adapter",config:{...n?{}:{dataObjectType:e.type,dataObjectConfig:{}}}}})}),t={};return i.forEach((e,i)=>{t[`${e.type.replace(/\./g,"_")}-${i}`]=e.value}),(0,c.jsx)(g.GridContentRenderer,{children:(0,c.jsx)(g.Grid,{autoWidth:!0,columns:n,data:[t]})})},H=e=>{var i;let{column:l,objectId:n,pipelineValue:t}=e,{t:a}=(0,m.useTranslation)(),{currentLanguage:o}=(0,N.useLanguageSelection)(),r=void 0!==t&&Object.keys(t).length>0?t:l.pipeline,s=!0===l.localizable?l.locale??o:void 0,{data:d,error:p,isFetching:h}=F.api.endpoints.dataObjectGetGridPreview.useQuery({body:{objectId:n,column:{type:l.type,key:l.key,locale:s,config:void 0!==r?{advancedColumns:r.sourceFields??[],transformers:r.transformers}:void 0}}}),x=(0,u.useRef)(d);if(void 0!==d&&(x.current=d),void 0!==p){let e="error"in p?p.error:a("data-hub.column-config-modal.preview.error");return(0,c.jsx)(g.Text,{type:"danger",children:e})}if(h&&void 0===x.current)return(0,c.jsx)(g.Text,{type:"secondary",children:a("data-hub.column-config-modal.preview.loading")});let f=null==(i=x.current)?void 0:i.value;return null!=f&&Array.isArray(f)&&0!==f.length?(0,c.jsx)(G,{value:f}):(0,c.jsx)(g.Text,{type:"secondary",children:a("data-hub.column-config-modal.preview.no-data")})},_=e=>{let{column:i,objectId:l,pipelineValue:n}=e,{t}=(0,m.useTranslation)(),[a,o]=(0,u.useState)(n);return(0,u.useEffect)(()=>{let e=setTimeout(()=>{o(n)},300);return()=>{clearTimeout(e)}},[n]),(0,c.jsx)(g.Box,{padding:{top:"small",bottom:"none",x:"small"},children:(0,c.jsxs)(g.Flex,{align:"center",gap:"small",children:[(0,c.jsxs)(g.Text,{style:{wordBreak:"keep-all"},children:[t("grid.advanced-column.preview"),":"]}),null===l?(0,c.jsx)(g.Text,{type:"secondary",children:t("data-hub.column-config-modal.preview.placeholder")}):(0,c.jsx)(H,{column:i,objectId:l,pipelineValue:a})]})})},R=e=>{let{column:i,entity:l,config:n,objectId:t,value:a,onChange:o,sourceFieldsRegistryId:r,transformersRegistryId:s}=e,{t:d}=(0,m.useTranslation)(),[p]=g.Form.useForm(),{compact:h}=$(),[x,f]=(0,u.useState)(a??{}),y=(0,c.jsx)(g.Pipeline.DynamicGroupItem,{dynamicTypeRegistryId:r,id:"sourceFields",showTitle:!h,translationKeyPrefix:"data-hub.column-config-modal.pipeline"}),v=(0,c.jsx)(g.Pipeline.DynamicGroupItem,{dynamicTypeRegistryId:s,id:"transformers",showTitle:!h,translationKeyPrefix:"data-hub.column-config-modal.pipeline"}),b=h?(0,c.jsx)(g.Tabs,{items:[{key:"sourceFields",label:d("data-hub.column-config-modal.pipeline.sourceFields"),forceRender:!0,children:y},{key:"transformers",label:d("data-hub.column-config-modal.pipeline.transformers"),forceRender:!0,children:v}]}):(0,c.jsx)(g.SplitLayout,{leftItem:{children:y,size:50},rightItem:{children:v,size:50},withDivider:!0});return(0,u.useEffect)(()=>{p.setFieldValue("value",a??{})},[a]),(0,c.jsx)(g.Form,{form:p,initialValues:{value:a??{}},layout:"vertical",onValuesChange:e=>{let i=p.getFieldValue("value");void 0===i||(0,M.isEqual)(x,i)||(f(i),null==o||o(i))},children:(0,c.jsx)(g.PipelineConfigProvider,{initialConfig:n??{},children:(0,c.jsx)(g.Form.Item,{name:"value",children:(0,c.jsx)(g.Pipeline,{items:[{id:"title",component:(0,c.jsx)(g.Pipeline.CustomItem,{children:(0,c.jsx)(g.Box,{padding:{top:"mini",bottom:"mini",x:"none"},children:(0,c.jsx)(g.Form.Item,{name:"title",children:(0,c.jsx)(g.Input,{placeholder:d("data-hub.column-config-modal.pipeline.title"),style:{maxWidth:"100%"}})})})})},{id:"fields",component:(0,c.jsx)(g.Pipeline.CustomItem,{children:b})},{id:"preview",component:(0,c.jsx)(g.Pipeline.CustomItem,{children:void 0!==i&&(0,c.jsx)(_,{column:i,objectId:t??null,pipelineValue:x})})}],value:a??{}})})})})},X=e=>{let{column:i,entity:l,objectId:n,onPipelineChange:t,sourceFieldsRegistryId:a,transformersRegistryId:o}=e;return(0,c.jsx)(R,{column:i,config:i.pipelineConfig,entity:l,objectId:n,onChange:e=>{t(i._id,e)},sourceFieldsRegistryId:a,transformersRegistryId:o,value:i.pipeline})};var O=l(8972);let W=e=>{let{value:i,onChange:l}=e,n=(0,O.useUser)(),t=["-",...Array.isArray(n.contentLanguages)?n.contentLanguages:[]];return(0,c.jsx)(g.LanguageSelection,{languages:t,onSelectLanguage:e=>{l("-"===e?null:e)},selectedLanguage:i??"-"})};var V=l(7925),U=l(5303),Y=l(5840);let Q=(0,Y.rU)(e=>{let{token:i,css:l}=e;return{body:l`
    display: flex;
    gap: ${i.marginSM}px;
    height: 100%;
  `,fieldsPanel:l`
    display: flex;
    flex-direction: column;
    gap: ${i.marginXS}px;
    width: 280px;
    height: 100%;
    padding-right: ${i.paddingSM}px;
    border-right: 1px solid ${i.colorBorderSecondary};
  `,list:l`
    flex: 1;
    min-width: 0;
    height: 100%;
    overflow-y: auto;
  `}}),K=e=>{let{groups:i,onColumnSelect:l,onClose:n}=e,{t}=(0,m.useTranslation)(),{styles:a}=Q();return(0,c.jsxs)("div",{className:a.fieldsPanel,children:[(0,c.jsx)(g.Header,{fullWidth:!0,title:t("data-hub.column-config-modal.fields-to-add"),children:(0,c.jsx)(g.Flex,{className:"w-full",justify:"flex-end",children:(0,c.jsx)(g.IconButton,{icon:{value:"collapse-sidebar",colorToken:"colorPrimary"},onClick:n})})}),(0,c.jsx)(g.ColumnPicker,{fillHeight:!0,groups:i,onSelect:e=>{void 0!==e.meta&&l(e.meta)}})]})};var q=l(3842);let J=[{key:"id",type:"system.id",group:["system"],config:[]},{key:"fullpath",type:"system.string",group:["system"],config:[]}],Z=(0,u.forwardRef)(function(e,i){var l;let{entity:n,classDefinitionId:t,columns:a,onApply:o,onCancel:r,hideToolbar:s=!1,sourceFieldsRegistryId:d,transformersRegistryId:p,language:h,onLanguageChange:x,exportableOnly:f=!1}=e,{t:y}=(0,m.useTranslation)(),{styles:v}=Q(),b=(0,O.useUser)(),[j,S]=(0,u.useState)(!0),C=h??(null==(l=b.contentLanguages)?void 0:l[0])??"en",[w,T]=(0,u.useState)(C),[k,$]=(0,u.useState)(!1);(0,u.useEffect)(()=>{null==x||x(w)},[]);let I=(0,u.useCallback)(e=>{T(e),null==x||x(e)},[x]),D=(0,u.useMemo)(()=>({currentLanguage:w,setCurrentLanguage:I,hasLocalizedFields:k,setHasLocalizedFields:$}),[w,I,k]),{draft:B,isLoading:G,objectId:H,columnGroups:_,onAddAdvancedColumn:R,openElementSelector:Y,handleAddColumnOfType:Z,handlePipelineChange:ee,handleRemove:ei,handleApply:el,handleDiscard:en,handleLocaleChange:et,handleReorder:ea,getColumns:eo}=(e=>{let{entity:i,classDefinitionId:l,columns:n,onApply:t,onCancel:a,exportableOnly:o=!1}=e,{getByName:r}=(0,N.useClassDefinitions)(),s=(0,u.useMemo)(()=>{var e;return(0,M.isNil)(l)?(null==(e=r(i))?void 0:e.id)??i:l},[l,i,r]),{data:d,isLoading:c}=F.api.endpoints.dataObjectGetAvailableGridColumns.useQuery({classId:s,folderId:1}),[p,m]=(0,u.useState)(()=>n.map(A));(0,u.useEffect)(()=>{m(n.map(A))},[n]);let[g,h]=(0,u.useState)(null),x=(0,u.useRef)(!1),{data:f}=F.api.endpoints.dataObjectGetGrid.useQuery({classId:s,body:{folderId:1,columns:J,filters:{includeDescendants:!0,page:1,pageSize:1}}},{skip:void 0===s});(0,u.useEffect)(()=>{var e;if(x.current)return;let i=null==f||null==(e=f.items)?void 0:e[0];(null==i?void 0:i.id)!==void 0&&h(i.id)},[null==f?void 0:f.items]);let{open:y}=(0,q.useElementSelector)({selectionType:q.SelectionType.Single,areas:{object:!0,asset:!1,document:!1},config:{objects:{allowedTypes:["object"],...void 0!==i?{allowedClasses:[i]}:{}}},onFinish:e=>{var i;let l=null==e||null==(i=e.items)?void 0:i[0];void 0!==l&&(x.current=!0,h(l.data.id))}}),v=(null==d?void 0:d.columns)??[];(0,u.useEffect)(()=>{0!==v.length&&m(e=>e.map(e=>{let i=v.find(i=>i.key===e.key||e.type===z&&i.type===e.type);return void 0===i?e:{...e,localizable:e.localizable??i.localizable,pipelineConfig:e.pipelineConfig??i.config}}))},[v]);let b=(0,u.useCallback)(e=>{m(i=>[...i,{_id:crypto.randomUUID(),key:e.key,fieldtype:e.key,type:e.type,pipelineConfig:e.config,localizable:e.localizable,isNew:!0}])},[]),j=(0,u.useMemo)(()=>o?v.filter(e=>!0===e.exportable):v,[v,o]),S=E(j),C=(0,u.useMemo)(()=>j.find(e=>e.type===z||e.key===P),[j]);return{draft:p,isLoading:c,objectId:g,availableFields:v,columnGroups:S,onAddAdvancedColumn:void 0!==C?()=>{b(C)}:void 0,openElementSelector:y,handleAddColumnOfType:b,handlePipelineChange:(e,i)=>{m(l=>l.map(l=>l._id===e?{...l,pipeline:i}:l))},handleRemove:e=>{m(i=>i.filter(i=>i._id!==e))},handleApply:()=>{t(p.filter(e=>""!==e.key).map(L))},handleDiscard:()=>{m(n.map(A)),a()},handleLocaleChange:(e,i)=>{m(l=>l.map(l=>l._id===e?{...l,locale:i}:l))},handleReorder:e=>{m(i=>e.map(e=>i.find(i=>i._id===e)).filter(e=>void 0!==e))},getColumns:()=>p.filter(e=>""!==e.key).map(L)}})({entity:n,classDefinitionId:t,columns:a,onApply:o,onCancel:r,exportableOnly:f});(0,u.useImperativeHandle)(i,()=>({getColumns:eo,addColumn:Z}),[eo,Z]);let er=B.map(e=>{var i,l;let t=e.type===z,a=""===e.key?"":e.key;return{id:e._id,sortable:!0,type:t?"collapse":"default",defaultActive:t&&!0===e.isNew,children:t?(0,c.jsx)(V.A,{color:"purple",children:(0,M.isNil)(null==(i=e.pipeline)?void 0:i.title)?a:String(null==(l=e.pipeline)?void 0:l.title)}):(0,c.jsx)(V.A,{children:a}),...t?{body:void 0!==e.pipelineConfig?(0,c.jsx)(X,{column:e,entity:n,objectId:H,onPipelineChange:ee,sourceFieldsRegistryId:d,transformersRegistryId:p}):(0,c.jsx)(g.Spin,{})}:{},renderRightToolbar:(0,c.jsxs)(g.Space,{size:"mini",children:[!0===e.localizable&&t&&(0,c.jsx)(W,{onChange:i=>{et(e._id,i)},value:e.locale}),(0,c.jsx)(g.IconButton,{icon:{value:"trash"},onClick:()=>{ei(e._id)},theme:"secondary"})]})}});return G?(0,c.jsx)(g.Flex,{align:"center",justify:"center",style:{minHeight:200},children:(0,c.jsx)(g.Spin,{})}):(0,c.jsx)(N.LanguageSelectionContext.Provider,{value:D,children:(0,c.jsx)(g.ContentLayout,{renderToolbar:s?void 0:(0,c.jsxs)(g.Toolbar,{padding:{x:"none",y:"small"},theme:"secondary",children:[(0,c.jsxs)(g.Flex,{gap:"mini",children:[(0,c.jsx)(g.IconTextButton,{icon:{value:"new"},onClick:()=>{S(e=>!e)},type:"default",children:y("data-hub.column-config-modal.add-column")}),void 0!==R&&(0,c.jsx)(g.IconTextButton,{icon:{value:"new"},onClick:R,type:"default",children:y("data-hub.column-config-modal.add-advanced-column")})]}),(0,c.jsxs)(g.Space,{size:"extra-small",children:[(0,c.jsx)(g.Button,{onClick:en,type:"default",children:y("data-hub.column-config-modal.discard")}),(0,c.jsx)(g.Button,{onClick:el,type:"primary",children:y("data-hub.column-config-modal.apply")})]})]}),renderTopBar:(0,c.jsxs)(g.Toolbar,{align:"center",position:"content",theme:"secondary",children:[(0,c.jsx)(g.Button,{onClick:Y,children:y("data-hub.column-config-modal.preview.select-object")}),(0,c.jsx)(N.LanguageSelectionWithProvider,{})]}),children:(0,c.jsx)(g.Content,{padded:!0,padding:{x:"none",y:"small"},style:{height:"calc(80vh - 200px)"},children:(0,c.jsxs)("div",{className:v.body,children:[!s&&j&&(0,c.jsx)(K,{groups:_,onClose:()=>{S(!1)},onColumnSelect:Z}),(0,c.jsx)("div",{className:v.list,children:(0,c.jsxs)(g.Space,{direction:"vertical",style:{width:"100%"},children:[0===B.length&&(0,c.jsx)(U.A,{image:U.A.PRESENTED_IMAGE_SIMPLE}),B.length>0&&(0,c.jsx)(g.StackList,{items:er,onItemsChange:e=>{ea(e.map(e=>String(e.id)))},sortable:!0})]})})]})})})})});var ee=l(1436),ei=l(969);let el=(0,Y.rU)(e=>{let{css:i}=e;return{fullWidth:i`
      width: 100%;
    `}}),en="YYYY-MM-DD HH:mm",et=()=>{let{t:e}=(0,m.useTranslation)(),{styles:i}=el(),[l]=g.Form.useForm(),{dateFrom:n,setDateFrom:t,dateTo:a,setDateTo:o,relatedObjectId:r,setRelatedObjectId:s,message:d,setMessage:u,pid:p,setPid:h,resetFilters:x,updateFilters:f,isLoading:y}=(0,ei.useFilter)();return(0,c.jsx)(g.ContentLayout,{renderToolbar:(0,c.jsxs)(g.Toolbar,{theme:"secondary",children:[(0,c.jsx)(g.IconTextButton,{disabled:y,icon:{value:"close"},onClick:()=>{x(),l.resetFields()},type:"link",children:e("sidebar.clear-all-filters")}),(0,c.jsx)(g.Button,{disabled:y,loading:y,onClick:f,type:"primary",children:e("button.apply")})]}),children:(0,c.jsx)(g.Content,{padded:!0,children:(0,c.jsx)(g.Form,{form:l,layout:"vertical",children:(0,c.jsxs)(g.Space,{className:i.fullWidth,direction:"vertical",size:"none",children:[(0,c.jsx)(g.Title,{children:e("application-logger.sidebar.search-parameter")}),(0,c.jsx)(g.Form.Item,{label:e("application-logger.filter.date-from"),name:"dateFrom",children:(0,c.jsx)(g.DatePicker,{className:"w-full",format:en,onChange:e=>{t(e)},outputType:"dateString",showTime:{format:"HH:mm"},value:n})}),(0,c.jsx)(g.Form.Item,{label:e("application-logger.filter.date-to"),name:"dateTo",children:(0,c.jsx)(g.DatePicker,{className:"w-full",format:en,onChange:e=>{o(e)},outputType:"dateString",showTime:{format:"HH:mm"},value:a})}),(0,c.jsx)(g.Form.Item,{label:e("application-logger.filter.priority"),name:"priority",children:(0,c.jsx)(ei.PrioritySelect,{})}),(0,c.jsx)(g.Form.Item,{label:e("application-logger.filter.message"),name:"message",children:(0,c.jsx)(g.Input,{onChange:e=>{let i=e.target.value;u(""===i?null:i)},value:d??void 0})}),(0,c.jsx)(g.Form.Item,{label:e("application-logger.filter.related-object-id"),name:"relatedObjectId",children:(0,c.jsx)(g.Input,{min:"0",onChange:e=>{let i=e.target.value;s(""===i?null:Number.parseInt(i))},step:"1",type:"number",value:r??void 0})}),(0,c.jsx)(g.Form.Item,{label:e("application-logger.filter.pid"),name:"pid",children:(0,c.jsx)(g.Input,{min:"0",onChange:e=>{let i=e.target.value;h(""===i?null:Number.parseInt(i))},step:"1",type:"number",value:p??void 0})})]})})})})},ea=e=>{let{children:i}=e,[l,n]=(0,u.useState)("filter"),t=(0,u.useMemo)(()=>({entries:[],buttons:[],sizing:"default",highlights:[],activeTab:l,setEntries:()=>{},setButtons:()=>{},setSizing:()=>{},setHighlights:()=>{},setActiveTab:n,addEntry:()=>{},removeEntry:()=>{},addButton:()=>{},removeButton:()=>{},toggleHighlight:()=>{},openTab:e=>{n(e)},closeTab:()=>{n("")},toggleTab:e=>{n(i=>i===e?"":e)}}),[l]);return(0,c.jsx)(g.SidebarContext.Provider,{value:t,children:i})},eo=e=>(0,c.jsx)(ei.FilterProvider,{children:(0,c.jsx)(er,{...e})}),er=e=>{let{componentPrefix:i,configName:l}=e,{t:n}=(0,m.useTranslation)(),t=[{key:"filter",icon:(0,c.jsx)(g.Icon,{options:{width:"16px",height:"16px"},value:"filter"}),component:(0,c.jsx)(et,{})}],a=(0,m.useAppDispatch)(),[o,r]=(0,u.useState)(1),[s,d]=(0,u.useState)(20),[p,h]=(0,u.useState)([]),{columnFilters:x,setIsLoading:f}=(0,ei.useFilter)(),y=[...x,{key:"component",type:"equals",filterValue:i+l}],{data:v,isFetching:b}=(0,ei.useBundleApplicationLoggerGetCollectionQuery)({body:{filters:{page:o,pageSize:s,columnFilters:y,sortFilter:(0,ei.mapSortingToSortFilter)(p)}}}),j=(null==v?void 0:v.totalItems)??0,S=(0,u.useCallback)(()=>{a(ei.api.util.invalidateTags(ee.invalidatingTags.APPLICATION_LOGGER()))},[a]),{refreshInterval:C,setRefreshInterval:w}=(e=>{let[i,l]=(0,u.useState)(void 0),n=(0,u.useCallback)(e,[e]);return(0,u.useEffect)(()=>{if((0,M.isNil)(i))return;let e=setInterval(()=>{n()},1e3*Number.parseInt(i));return()=>{clearInterval(e)}},[i,n]),{refreshInterval:i,setRefreshInterval:l}})(S);return(0,u.useEffect)(()=>{f(b)},[b]),(0,c.jsx)(ea,{children:(0,c.jsx)(g.ContentLayout,{className:"h-full",renderSidebar:(0,c.jsx)(g.Sidebar,{entries:t}),renderToolbar:(0,c.jsxs)(g.Toolbar,{justify:"space-between",theme:"secondary",children:[(0,c.jsxs)(g.Flex,{align:"center",gap:8,children:[!(0,M.isNil)(C)&&(0,c.jsx)("span",{children:n("application-logger.refresh-interval")}),(0,c.jsx)(g.CreatableSelect,{allowClear:!0,inputType:"number",minWidth:200,numberInputProps:{min:1},onChange:w,onCreateOption:e=>({value:e,label:n("application-logger.refresh-interval.seconds",{seconds:e})}),options:[{value:"3",label:n("application-logger.refresh-interval.seconds",{seconds:3})},{value:"5",label:n("application-logger.refresh-interval.seconds",{seconds:5})},{value:"10",label:n("application-logger.refresh-interval.seconds",{seconds:10})},{value:"30",label:n("application-logger.refresh-interval.seconds",{seconds:30})},{value:"60",label:n("application-logger.refresh-interval.seconds",{seconds:60})}],placeholder:n("application-logger.refresh-interval.select"),validate:e=>!Number.isNaN(Number.parseInt(e))&&Number.parseInt(e)>0,value:C})]}),(0,c.jsxs)(g.Flex,{children:[(0,c.jsx)(g.IconButton,{disabled:b,icon:{value:"refresh"},onClick:S}),j>0&&(0,c.jsxs)(c.Fragment,{children:[(0,c.jsx)(g.Divider,{size:"small",type:"vertical"}),(0,c.jsx)(g.Pagination,{current:o,defaultPageSize:s,onChange:(e,i)=>{r(e),d(i)},showSizeChanger:!0,showTotal:e=>n("pagination.show-total",{total:e}),total:j})]})]})]}),children:(0,c.jsx)(g.Content,{padded:!0,children:(0,c.jsx)(ei.ApplicationLoggerTable,{isLoading:b,items:(null==v?void 0:v.items)??[],onSortingChange:e=>{h(e),r(1)},sorting:p})})})})}}}]);
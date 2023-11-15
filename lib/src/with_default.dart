import 'nullable.dart';
import 'schema.dart';
import 'transform.dart';

extension OvoWithDefault<T> on OvoSchema<T> {
  OvoSchema<T> withDefault(T defaultValue) =>
      nullable().transform<T>((context, data) => data ?? defaultValue);
}
